<?php

namespace App\Http\Controllers;

use App\Models\Cafeteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CafeteriaController extends Controller
{
    /**
     * Export cafeteria entries.
     *
     * Sql queries:
     *
     * SELECT account.name,
     *        pocket.calendar_year,
     *        cafeteria.jan,
     *        cafeteria.feb,
     *        cafeteria.mar,
     *        cafeteria.apr,
     *        cafeteria.may,
     *        cafeteria.jun,
     *        cafeteria.jul,
     *        cafeteria.aug,
     *        cafeteria.sep,
     *        cafeteria.oct,
     *        cafeteria.nov,
     *        cafeteria.dec
     * FROM cafeteria
     * JOIN account ON cafeteria .account_id = account.id
     * JOIN pocket ON cafeteria.pocket_id = pocket.id
     * WHERE pocket.calendar_year = ?
     *
     * @param  json["year": integer]
     *
     * @return json[{
     */
    public function export(Request $request)
    {
        return response()->json(
            DB::table('cafeteria')
                ->join('account', 'cafeteria.account_id', '=', 'account.id')
                ->join('pocket', 'cafeteria.pocket_id', '=', 'pocket.id')
                ->where('pocket.calendar_year', '=', $request->input('year'))
                ->select(
                    'account.name as user',
                    'pocket.calendar_year as year',
                    'cafeteria.jan',
                    'cafeteria.feb',
                    'cafeteria.mar',
                    'cafeteria.apr',
                    'cafeteria.may',
                    'cafeteria.jun',
                    'cafeteria.jul',
                    'cafeteria.aug',
                    'cafeteria.sep',
                    'cafeteria.oct',
                    'cafeteria.nov',
                    'cafeteria.dec',
                )->get()
        );
    }

    /**
     * Read (or create if not exists) cafeteria entries.
     *
     * Sql queries:
     *
     * SELECT count(pocket.id)
     * FROM pocket
     * JOIN cafeteria ON pocket.id = cafeteria.pocket_id
     * WHERE cafeteria.account_id = ? AND
     *       pocket.calendar_year = ?;
     *
     * SELECT pocket.id
     * FROM pocket
     * WHERE calendar_year = ?;
     *
     * SELECT pocket.id, pocket.limit, pocket.name, cafeteria.*
     * FROM cafeteria
     * JOIN pocket ON cafeteria.pocket_id = pocket.id
     * WHERE cafeteria.account_id = ? AND
     *       pocket.calendar_year = ?;
     *
     * @param  json{"account_id: string, "year": integer}
     *
     * @return json[{
     *              "cafeteria_id": string,
     *              "limit":        integer,
     *              "name":         string,
     *              "jan":          integer,
     *              "feb":          integer,
     *              "mar":          integer,
     *              "apr":          integer,
     *              "may":          integer,
     *              "jun":          integer,
     *              "jul":          integer,
     *              "aug":          integer,
     *              "sep":          integer,
     *              "oct":          integer,
     *              "nov":          integer,
     *              "dec":          integer
     *             }]
     */
    public function read(Request $request)
    {
        DB::transaction(function () use ($request) {
            $count = DB::table('pocket')
                ->join('cafeteria', 'pocket.id', '=', 'cafeteria.pocket_id')
                ->where('cafeteria.account_id', '=', $request->input('account_id'))
                ->where('pocket.calendar_year', '=', $request->input('year'))
                ->select('pocket.id')
                ->count()
            ;
            // Create records with defults
            if (0 === $count) {
                $pocketIds = DB::table('pocket')
                    ->where('calendar_year', '=', $request->input('year'))
                    ->select('pocket.id')
                    ->get()
                ;
                foreach ($pocketIds as $pocketId) {
                    $Cafeteria = new Cafeteria();
                    $Cafeteria->account_id = $request->input('account_id');
                    $Cafeteria->pocket_id  = $pocketId->id;
                    $Cafeteria->save();
                }
            }
        });

        return response()->json(
            DB::table('cafeteria')
                ->join('pocket', 'cafeteria.pocket_id', '=', 'pocket.id')
                ->where('cafeteria.account_id', '=', $request->input('account_id'))
                ->where('pocket.calendar_year', '=', $request->input('year'))
                ->select(
                    'cafeteria.id as cafeteria_id',
                    'pocket.limit',
                    'pocket.name',
                    'cafeteria.jan',
                    'cafeteria.feb',
                    'cafeteria.mar',
                    'cafeteria.apr',
                    'cafeteria.may',
                    'cafeteria.jun',
                    'cafeteria.jul',
                    'cafeteria.aug',
                    'cafeteria.sep',
                    'cafeteria.oct',
                    'cafeteria.nov',
                    'cafeteria.dec',
                )
                ->get()
        );
    }

    /**
     * Update cafeteria entries.
     *
     * Sql queries:
     *
     * SELECT pocket.id, pocket.limit
     * FROM cafeteria
     * JOIN pocket ON cafeteria.pocket_id = pocket.id
     * WHERE cafeteria.account_id = ? AND
     *       cafeteria.calendar_year = ?;
     *
     * SELECT limit
     * FROM calendar
     * WHERE year = ?;
     *
     * UPDATE cafeteria
     * SET jan = ?,
     *     feb = ?,
     *     mar = ?,
     *     apr = ?,
     *     may = ?,
     *     jun = ?,
     *     jul = ?,
     *     aug = ?,
     *     sep = ?,
     *     oct = ?,
     *     nov = ?,
     *     dec = ?
     * WHERE id = ?;
     *
     * @param  json{"acount_id":     string,
     *              "year":          number,
     *              "cafeteria":     [{
     *                                  "cafeteria_id": string,
     *                                  "jan":          number,
     *                                  "feb":          number,
     *                                  "mar":          number,
     *                                  "apr":          number,
     *                                  "may":          number,
     *                                  "jun":          number,
     *                                  "jul":          number,
     *                                  "aug":          number,
     *                                  "sep":          number,
     *                                  "Oct":          number,
     *                                  "Nov":          number,
     *                                  "Dec":          number
     *                               ]]
     */
    public function write(Request $request)
    {
        // Validate request
        foreach ($request->input('cafeteria') as $cafeteria) {
            if (0 > $cafeteria['jan']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['jan' => $cafeteria['jan']],
                ], 400);
            }
            if (0 > $cafeteria['feb']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['feb' => $cafeteria['feb']],
                ], 400);
            }
            if (0 > $cafeteria['mar']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['mar' => $cafeteria['mar']],
                ], 400);
            }
            if (0 > $cafeteria['apr']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['apr' => $cafeteria['apr']],
                ], 400);
            }
            if (0 > $cafeteria['may']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['may' => $cafeteria['may']],
                ], 400);
            }
            if (0 > $cafeteria['jun']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['jun' => $cafeteria['jun']],
                ], 400);
            }
            if (0 > $cafeteria['jul']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['jul' => $cafeteria['jul']],
                ], 400);
            }
            if (0 > $cafeteria['aug']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['aug' => $cafeteria['aug']],
                ], 400);
            }
            if (0 > $cafeteria['sep']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['sep' => $cafeteria['sep']],
                ], 400);
            }
            if (0 > $cafeteria['oct']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['oct' => $cafeteria['oct']],
                ], 400);
            }
            if (0 > $cafeteria['nov']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['nov' => $cafeteria['nov']],
                ], 400);
            }
            if (0 > $cafeteria['dec']) {
                return response()->json([
                    'exception' => 'Bad request',
                    'file' => 'CafeteriaController.php',
                    'line' => 0,
                    'message' => ['dec' => $cafeteria['dec']],
                ], 400);
            }
        }

        // Bussiness logic
        $pocketLimits = DB::table('cafeteria')
            ->join('pocket', 'cafeteria.pocket_id', '=', 'pocket.id')
            ->where('cafeteria.account_id', '=', $request->input('account_id'))
            ->where('pocket.calendar_year', '=', $request->input('year'))
            ->select('cafeteria.id', 'pocket.limit')
            ->get()
        ;

        $yearLimit = DB::table('calendar')
            ->where('year', '=', $request->input('year'))
            ->select('limit')
            ->first()
        ;

        $sum = 0;

        foreach ($request->input('cafeteria') as $cafeteria) {
            $pocketSum =
                $cafeteria['jan'] +
                $cafeteria['feb'] +
                $cafeteria['mar'] +
                $cafeteria['apr'] +
                $cafeteria['may'] +
                $cafeteria['jun'] +
                $cafeteria['jul'] +
                $cafeteria['aug'] +
                $cafeteria['sep'] +
                $cafeteria['oct'] +
                $cafeteria['nov'] +
                $cafeteria['dec'];
            $sum += $pocketSum;

            // Pocket limit
            foreach ($pocketLimits as $limit) {
                if ($limit->id === $cafeteria['cafeteria_id']) {
                    if ($limit->limit < $pocketSum) {
                        return response()->json([
                            'exception' => 'Bad request',
                            'file' => 'CafeteriaController.php',
                            'line' => 0,
                            'message' => ['cafeteria_id' => $cafeteria['cafeteria_id']],
                        ], 400);
                    }
                }
            }
        }
        // Yearly limit
        if ($sum > $yearLimit->limit) {
            return response()->json([
                'exception' => 'Bad request',
                'file' => 'CafeteriaController.php',
                'line' => 0,
                'message' => ['year' => $request->input('year')],
            ], 400);
        }

        // Write
        DB::transaction(function () use ($request) {
            foreach ($request->input('cafeteria') as $cafeteria) {
                DB::table('cafeteria')
                    ->where('id', $cafeteria['cafeteria_id'])
                    ->update([
                        'jan' => $cafeteria['jan'],
                        'feb' => $cafeteria['feb'],
                        'mar' => $cafeteria['mar'],
                        'apr' => $cafeteria['apr'],
                        'may' => $cafeteria['may'],
                        'jun' => $cafeteria['jun'],
                        'jul' => $cafeteria['jul'],
                        'aug' => $cafeteria['aug'],
                        'sep' => $cafeteria['sep'],
                        'oct' => $cafeteria['oct'],
                        'nov' => $cafeteria['nov'],
                        'dec' => $cafeteria['dec'],
                    ])
                ;
            }
        });

        return response()->json([], 204);
    }
}
