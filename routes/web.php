<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Administrator area
Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', function () {
        return view('administrator.auth.login');
    })->name('admin.login');

    Route::get('/dashboard', function () {
        return view('administrator.dashboard.index');
    })->middleware('auth:admin')->name('admin.dashboard');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('admin.logout');
});
