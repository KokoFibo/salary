<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SlipgajiController;

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
Route::get('/karyawan', [ApiController::class, 'index']);
// Route::post('store/{id}', [ApiController::class, 'store']);
Route::get('getkaryawan/{id}', [ApiController::class, 'getDataKaryawan']);
Route::get('getuser/{id}', [ApiController::class, 'getDataUser']);
Route::delete('delete_karyawan_yf_aja/{id}', [ApiController::class, 'delete_data_karyawan_yf_aja']);
Route::delete('delete_user_yf_aja/{id}', [ApiController::class, 'delete_data_user_yf_aja']);
Route::post('store/{karyawan}', [ApiController::class, 'store']);

// Slip Gaji Controller
Route::get('slipgaji/getdata/{id}/{month}/{year}', [SlipgajiController::class, 'getData']);
Route::post('login', [AuthController::class, 'login']);
