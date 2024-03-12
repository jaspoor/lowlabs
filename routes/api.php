<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProcessController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\TagController;

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
Route::middleware(['auth:sanctum', 'ability:api'])->group(function () {

    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/user', function(Request $request) {
            return $request->user();
    });

    Route::resource('clients', ClientController::class)->except([
            'create', 'edit'
        ]);

    Route::get('clients/{client}/processes', [ClientController::class, 'processes'])
        ->name('clients.processes');

    Route::resource('tags', TagController::class)->except([
            'create', 'edit'
        ]);

    Route::resource('processes', ProcessController::class)->except([
            'create', 'edit'
        ]);

    Route::resource('processes/{process}/records', RecordController::class)->except([
            'create', 'edit'
        ]);
});