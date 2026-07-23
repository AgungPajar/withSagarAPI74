<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\AdminAuthController;
use App\Http\Controllers\Web\Admin\AdminDashboardController;
use App\Http\Controllers\Web\Admin\AdminJurusanController;
use App\Http\Controllers\Web\Admin\AdminKelasController;
use App\Http\Controllers\Web\Admin\AdminStudentController;
use App\Http\Controllers\Web\Admin\AdminClubController;

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
    
    // Redirect /admin to dashboard or login
    Route::get('/', function () {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    });

    // Auth Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        
        // CRUD Routes
        Route::get('import/status', [AdminKelasController::class, 'importStatus'])->name('admin.import.status');
        
        Route::post('jurusan/reorder', [AdminJurusanController::class, 'reorder'])->name('admin.jurusan.reorder');
        Route::resource('jurusan', AdminJurusanController::class, ['as' => 'admin']);
        
        Route::post('kelas/{kela}/import', [AdminKelasController::class, 'importSiswa'])->name('admin.kelas.import');
        Route::resource('kelas', AdminKelasController::class, ['as' => 'admin']);
        
        Route::resource('siswa', AdminStudentController::class, ['as' => 'admin']);
        
        Route::resource('ekskul', AdminClubController::class, ['as' => 'admin']);
    });
});
