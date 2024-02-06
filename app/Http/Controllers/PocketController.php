<?php

namespace App\Http\Controllers;

use App\Models\Pocket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PocketController extends Controller
{
    /**
     * Create resource in storage.
     *
     * @param  json{"limit": integer, "name": string, "year": integer}
     */
    public function create(Request $request)
    {
        // Validate request
        if (0 >= $request->input('limit')) {
            return response()->json([
                'exception' => 'Bad request',
                'file' => 'PocketController.php',
                'line' => 0,
                'message' => ['limit' => $request->input('limit')],
            ], 400);
        }
        if (1 > strlen($request->input('name'))) {
            return response()->json([
                'exception' => 'Bad request',
                'file' => 'PocketController.php',
                'line' => 0,
                'message' => ['name' => $request->input('name')],
            ], 400);
        }
        if (0 >= $request->input('year')) {
            return response()->json([
                'exception' => 'Bad request',
                'file' => 'PocketController.php',
                'line' => 0,
                'message' => ['message' => ['year' => $request->input('year')]],
            ], 400);
        }

        $pocket = new Pocket();
        $pocket->calendar_year = $request->input('year');
        $pocket->limit         = $request->input('limit');
        $pocket->name          = $request->input('name');

        DB::transaction(function () use ($pocket) {
            $pocket->save();
        });

        return response()->json([], 204);
    }

    /**
     * Retur'message' => [n a listing of the p,
     *]ockets.
     *
     * Sql queries
     *
     * SELECT *
     * FROM Pockets
     * WHERE calendar_year = ?;
     *
     * @param  json{"year": integer}
     *
     * @return json[{"id", string, "calendar_year": integer}, "limit": integer, "name": string}]
     */
    public function list(Request $request)
    {
        return response()->json(Pocket::where('calendar_year', '=', $request->input('year'))->get());
    }
}
