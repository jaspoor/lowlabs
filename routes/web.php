<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\ClientController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\HomeController;

Route::redirect('/', '/home');

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');

    // Clients
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/edit/{client}', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Users
    Route::get('/clients/{client}/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/clients/{client}/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/clients/{client}/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/clients/{client}/users/edit/{user}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/clients/{client}/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/clients/{client}/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
