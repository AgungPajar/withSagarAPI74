<?php

use Illuminate\Http\Request;
use App\Models\ActivityReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClubRequestController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\AktivityReportController;
use App\Http\Controllers\LombaRegistrationController;
use App\Http\Controllers\Admin\ClubController as AdminClubController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register-siswa', [RegistrationController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/clubs', [ClubController::class, 'index']);

Route::get('/export/harian', [ExportExcelController::class, 'exportHarian']);
Route::get('/rekap/export/monthly', [ExportExcelController::class, 'exportBulanan']);

Route::prefix('admin')->middleware('auth:sanctum',)->group(function () {
  Route::get('/clubs', [ClubController::class, 'index']);
  Route::post('/clubs', [ClubController::class, 'store']);
  Route::delete('/clubs/{club}', [ClubController::class, 'destroy']);
  Route::post('/clubs/update', [ClubController::class, 'update']);

  Route::post('/users', [UserController::class, 'store']);

  Route::post('/students/import', [StudentController::class, 'importExcel']);

  Route::post('/students/naik-kelas', [StudentController::class, 'promote']);
  Route::get('/students/class/{class}', [StudentController::class, 'getByClass']);
  Route::get('/jurusans', [JurusanController::class, 'index']);
  // Route::get('/majors', [MajorController::class, 'index']);

  Route::get('/students', [StudentController::class, 'indexStudents']);

  
});
Route::get('/students/download-template', [StudentController::class, 'downloadTemplate']);

Route::middleware('auth:sanctum')->group(function () {
  Route::get('/user', fn($request) => $request->user());

  Route::get('/clubs/{hashedId}', [ClubController::class, 'show']);
  Route::get('/clubs/{hashedId}/students', [ClubController::class, 'getStudents']);
  Route::post('/clubs/{hashedId}/members', [StudentController::class, 'storeToClub']);
  Route::get('/clubs/{hashedId}/members', [ClubRequestController::class, 'getAcceptedMembers']);

  // Route::get('/clubs/{clubId}/requests', [ClubRequestController::class, 'index']);
  Route::post('/clubs/{hashedId}/request-join', [ClubRequestController::class, 'requestJoin']);
  Route::get('/clubs/{hashedId}/requests', [ClubRequestController::class, 'pendingRequests']);
  Route::post('/clubs/{clubId}/requests/{requestId}/confirm', [ClubRequestController::class, 'confirmRequest']);
  Route::delete('/clubs/{clubId}/requests/{requestId}', [ClubRequestController::class, 'deleteMember']);


  Route::post('profile/update', [ClubController::class, 'update']);

  Route::post('/attendances', [AttendanceController::class, 'store']);
  Route::post('/clubs/{hashedId}/activity-reports', [AktivityReportController::class, 'store']);

  Route::get('/student/{studentHashId}/dashboard', [StudentController::class, 'dashboard']);
  Route::get('/student/{hashedId}', [StudentController::class, 'show']);
  route::apiResource('students', StudentController::class)->only(['destroy']);

  Route::get('/rekapitulasi', [RekapController::class, 'index']);
});

Route::get('/pendaftaran', [LombaRegistrationController::class, 'index']);
Route::post('/daftar-lomba', [LombaRegistrationController::class, 'store']);
Route::put('/pendaftaran/{id}', [LombaRegistrationController::class, 'update']);
Route::delete('/pendaftaran/{id}', [LombaRegistrationController::class, 'destroy']);
