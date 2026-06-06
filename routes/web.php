<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Pedagang\PedagangController;
use App\Http\Controllers\Pengawas\PengawasController;
use Illuminate\Support\Facades\Route;

// ── Landing Page ──────────────────────────────────────────────────────────
Route::get('/', function () { return view('welcome'); })->name('welcome');

// ── Auth ──────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Pedagang ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pedagang'])->prefix('pedagang')->name('pedagang.')->group(function () {
    Route::get('/dashboard',   [PedagangController::class, 'dashboard'])->name('dashboard');
    Route::get('/pembayaran',  [PedagangController::class, 'pembayaran'])->name('pembayaran');
    Route::post('/pembayaran', [PedagangController::class, 'prosesBayar'])->name('proses-bayar');
    Route::get('/riwayat',     [PedagangController::class, 'riwayat'])->name('riwayat');
});

// ── Admin ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',   [AdminController::class, 'dashboard'])->name('dashboard');

    // Verifikasi
    Route::get('/verifikasi',  [AdminController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/verifikasi', [AdminController::class, 'prosesVerifikasi'])->name('proses-verifikasi');

    // Users
    Route::get('/users',            [AdminController::class, 'users'])->name('users');
    Route::post('/users/add',       [AdminController::class, 'addUser'])->name('add-user');
    Route::post('/users/update',    [AdminController::class, 'updateUser'])->name('update-user');
    Route::delete('/users/delete',  [AdminController::class, 'deleteUser'])->name('delete-user');
    Route::post('/users/reset-pw',  [AdminController::class, 'resetPassword'])->name('reset-pw');

    // Kios
    Route::get('/kios',           [AdminController::class, 'kios'])->name('kios');
    Route::post('/kios/tambah',   [AdminController::class, 'tambahKios'])->name('tambah-kios');
    Route::post('/kios/edit',     [AdminController::class, 'editKios'])->name('edit-kios');
    Route::post('/kios/hapus',    [AdminController::class, 'hapusKios'])->name('hapus-kios');

    // Monitoring & Laporan
    Route::get('/monitoring',   [AdminController::class, 'monitoring'])->name('monitoring');
    Route::get('/laporan',      [AdminController::class, 'laporan'])->name('laporan');

    // Setting
    Route::get('/setting',              [AdminController::class, 'setting'])->name('setting');
    Route::post('/setting/save',        [AdminController::class, 'saveSetting'])->name('save-setting');
    Route::post('/setting/tambah',      [AdminController::class, 'tambahSetting'])->name('tambah-setting');
    Route::post('/setting/hapus',       [AdminController::class, 'hapusSetting'])->name('hapus-setting');
    Route::post('/setting/generate',    [AdminController::class, 'generateTagihan'])->name('generate-tagihan');
});

// ── Pengawas ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pengawas'])->prefix('pengawas')->name('pengawas.')->group(function () {
    Route::get('/dashboard',   [PengawasController::class, 'dashboard'])->name('dashboard');
    Route::get('/monitoring',  [PengawasController::class, 'monitoring'])->name('monitoring');
    Route::get('/laporan',     [PengawasController::class, 'laporan'])->name('laporan');
    Route::post('/kirim-notifikasi', [PengawasController::class, 'kirimNotifikasi'])->name('kirim-notifikasi');
});
