<?php

use App\Http\Controllers\api\BibitController;
use App\Http\Controllers\api\KelompokTaniController;
use App\Http\Controllers\api\KomoditasController;
use App\Http\Controllers\api\LaporanBibitController;
use App\Http\Controllers\api\NotificationController;
use App\Http\Controllers\api\PenyuluhController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\Auth\AuthApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;

/**
 * Route untuk fungsional login, register, otp, dan reset password
 */
Route::controller(AuthApiController::class)->group(function () {
    Route::post('login', 'login')->withoutMiddleware(JwtMiddleware::class)->name('login.mobile');
    Route::post('register', 'register')->withoutMiddleware(JwtMiddleware::class)->name('register.mobile');
    Route::post('validate-phone', 'validatePhone')->withoutMiddleware(JwtMiddleware::class)->name('validate-phone.mobile');
    Route::post('forgot-password', 'sendOtp')->withoutMiddleware(JwtMiddleware::class)->name('forgot-password.mobile');
    Route::post('verify-otp', 'validateOtp')->withoutMiddleware(JwtMiddleware::class)->name('verify-otp.mobile');
    Route::post('reset-password', 'passwordReset')->withoutMiddleware(JwtMiddleware::class)->name('reset-password.mobile');
    Route::patch('users/reset-password', 'updatePasswordViaProfile')->withoutMiddleware(JwtMiddleware::class)->name('reset-password.profile.mobile');
});

Route::controller(NotificationController::class)->group(function () {
    Route::post('fcm-token/store', 'storeFcmToken')->name('store-fcm-token');
    Route::get('notifications', 'getUserNotification');
    Route::patch('notifications/{id}/read', 'markAsReadNotification');
    Route::delete('notifications/{id}', 'destroy');
});

/**
 * Route untuk memperbarui data penyuluh terdaftar.
 * Gunakan jika waktu proses registrasi data penyuluh tidak valid
 */
Route::put('users/penyuluh-terdaftar/{id}', [PenyuluhController::class, 'update'])->withoutMiddleware(JwtMiddleware::class);

/**
 * Route untuk mengambil data profil
 */
Route::get('users/{id}', [UserController::class, 'getProfile']);

/**
 * Route untuk mengambil data bibit berkualitas
 */
Route::controller(BibitController::class)->group(function () {
    Route::get('bibit', 'getAll');
    Route::get('bibit/count', 'calculateTotal');
});

/**
 * Route untuk mengambil seluruh data komoditas dan berdasarkan id komoditas
 */
Route::controller(KomoditasController::class)->group(function () {
    Route::get('komoditas/musim', 'getMusim');
    Route::get('komoditas/count', 'calculateTotal');
    Route::get('komoditas/bibit', 'getAllWithBibit');
    Route::get('komoditas/{id}','show');
    Route::get('komoditas','getAll');
});

/**
 * Route untuk mengambil data kelompok tani berdasarkan id penyuluh dan id kelompok tani
 */
Route::controller(KelompokTaniController::class)->group(function () {
    Route::get('kelompok-tani/kecamatan/{id}/count','countByKecamatanId');
    Route::get('kelompok-tani/kecamatan/{id}', 'getAllByKecamatanId');
    Route::get('kelompok-tani/count','calculateTotal');
    Route::get('kelompok-tani/{id}', 'getById');
    Route::get('kelompok-tani', 'getAllByPenyuluhId');
});

/**
 * Route untuk mengirim laporan bibit dan mengambil history laporan bibit berdasarkan id penyuluh
 */
Route::controller(LaporanBibitController::class)->group(function () {
    Route::post('laporan-kondisi', 'saveReport');
    Route::get('laporan-kondisi/count/kecamatan/{id}', 'getTotalByKecamatanId');
    Route::get('laporan-kondisi/count/{id}', 'getLaporanStatusCounts');
    Route::get('history-laporan/{id}', 'getByKecamatanId');
});
