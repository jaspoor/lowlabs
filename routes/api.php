<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ClientRecordController;
use App\Http\Controllers\Api\GptController;
use App\Http\Controllers\Api\ProcessController;
use App\Http\Controllers\Api\ProcessRecordController;
use App\Http\Controllers\Api\RecipeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TagController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('request', [AuthController::class, 'request']);
    Route::post('activate', [AuthController::class, 'activate']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

// TODO: add route group to middleware('jwt.auth')
Route::name('api.')->group(function () {
    Route::get('clients/{client}/recipes', [ClientController::class, 'recipes'])->name('clients.recipes');

    Route::post('/gpt', [GptController::class, 'complete']);
});

Route::middleware('auth:sanctum')->name('api.')->group(function () {

    Route::post('/login', [LoginController::class, 'login']);

        
    Route::get('/user', function(Request $request) {
            return $request->user();
    });

    Route::resource('clients', ClientController::class)->except([
            'create', 'edit'
        ]);

    Route::resource('clients/{client}/records', ClientRecordController::class)->except([
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

    Route::resource('recipes', RecipeController::class)->except([
        'create', 'edit'
    ]);

    Route::resource('processes/{process}/records', ProcessRecordController::class)->except([
            'create', 'edit'
        ]);
});