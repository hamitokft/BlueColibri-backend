<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CafeteriaController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PocketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/account/create',     [AccountController::class,   'create']);
Route::post('/account/list',       [AccountController::class,   'list']);
Route::post('/cafeteria/export',   [CafeteriaController::class, 'export']);
Route::post('/cafeteria/read',     [CafeteriaController::class, 'read']);
Route::post('/cafeteria/write',    [CafeteriaController::class, 'write']);
Route::post('/calendar/available', [CalendarController::class,  'available']);
Route::post('/calendar/create',    [CalendarController::class,  'create']);
Route::post('/calendar/list',      [CalendarController::class,  'list']);
Route::post('/pocket/create',      [PocketController::class,    'create']);
Route::post('/pocket/list',        [PocketController::class,    'list']);
