<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;

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
Route::middleware('auth:api')->get('/user', function(Request $request) {
    return $request->user();
});

Route::controller(RecordController::class)->group(function () {
    Route::get('/records', 'index');
    Route::post('/records', 'store');
    Route::get('/records/{record}', 'show');
    Route::delete('/records/{record}', 'delete');
});