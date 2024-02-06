<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Return a listing of the account entries.
     *
     * @param void
     *
     * @return json[{"id": string, "name": string }]
     */
    public function list()
    {
        return response()->json(Account::all());
    }

    /**
     * Create an account entry.
     *
     * @param  json{"name": string}
     */
    public function create(Request $request)
    {
        // Validate request
        if (1 > strlen($request->input('name'))) {
            return response()->json([
                'exception' => 'Bad request',
                'file' => 'AccountController.php',
                'line' => 0,
                'message' => ['name' => $request->input('name')],
            ], 400);
        }

        $account = new Account();
        $account->name = $request->input('name');

        DB::transaction(function () use ($account) {
            $account->save();
        });

        return response()->json([], 204);
    }
}
