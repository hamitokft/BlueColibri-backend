<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    /**
     * Return a listing of the available (when pockets created)
     * calendar entries.
     *
     * Sql queries
     *
     * SELECT DISTINCT calendar.*
     * FROM calendar
     * JOIN pocket ON pocket.calendar_year = calendar.year;
     *
     * @param void
     *
     * @return json[{"limit": integer, "year": integer}]
     */
    public function available()
    {
        $list = DB::table('Calendar')
            ->join('pocket', 'Calendar.year', '=', 'pocket.calendar_year')
            ->select('calendar.*')
            ->distinct()
            ->get()
        ;

        return response()->json($list);
    }

    /**
     * Create an calendar entry.
     *
     * @param  json{"limit": integer, "year": integer}
     */
    public function create(Request $request)
    {
        // Validate request
        if (0 >= $request->input('limit')) {
            // Emulate laravel exception format
            return response()->json([
                'exception' => 'Bad request',
                'file' => 'CalendarController.php',
                'line' => 0,
                'message' => ['limit' => $request->input('limit')],
            ], 400);
        }
        if (0 >= $request->input('year')) {
            return response()->json([
                'exception' => 'Bad request',
                'file' => 'CalendarController.php',
                'line' => 0,
                'message' => ['year' => $request->input('year')],
            ], 400);
        }

        $calendar = new Calendar();
        $calendar->limit = $request->input('limit');
        $calendar->year =  $request->input('year');

        DB::transaction(function () use ($calendar) {
            $calendar->save();
        });

        return response()->json([], 204);
    }

    /**
     * Return a listing of the calendar entries.
     *
     * @param void
     *
     * @return json[{"limit": integer, "year": integer}]
     */
    public function list()
    {
        return response()->json(Calendar::all());
    }
}
