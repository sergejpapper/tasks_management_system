<?php

use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\TasksController;
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

Route::controller(LoginRegisterController::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::get('/login', 'login')->name('login');
    Route::post('/authenticate', 'authenticate')->name('authenticate');
    Route::post('/store', 'store')->name('store');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(TasksController::class)->group(function () {
    Route::get('/tasks', 'index')->name('tasks');
    Route::post('/task', 'add');
    Route::delete('/task/{task}', 'delete');
    Route::get('/export', 'export');
});
