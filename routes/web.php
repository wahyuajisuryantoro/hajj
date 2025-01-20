<?php

use App\Http\Controllers\LandingController;
use App\Http\Controllers\Member_AccountController;
use App\Http\Controllers\Member_AuthController;
use App\Http\Controllers\Member_BonusController;
use App\Http\Controllers\Member_CustomerController;
use App\Http\Controllers\Member_DashboardController;
use App\Http\Controllers\Member_JamaahController;
use App\Http\Controllers\Member_MarketingToolsController;
use App\Http\Controllers\Member_MitraController;
use App\Http\Controllers\Member_ProgramController;
use App\Http\Controllers\PanduanController;
use Illuminate\Support\Facades\Route;


// Public routes
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/login', [Member_AuthController::class, 'index'])->name('root');
Route::post('/login', [Member_AuthController::class, 'login'])->name('mitra.login');
Route::post('/logout', [Member_AuthController::class, 'logout'])->name('mitra.logout');
Route::get('/programs/{id}', [LandingController::class, 'showProgram'])->name('programs.show');
Route::get('/news/{id}', [LandingController::class, 'showNews'])->name('news.show');
Route::get('/programs', [LandingController::class, 'allPrograms'])->name('programs.index');
Route::get('/berita', [LandingController::class, 'allBerita'])->name('news.index');
Route::get('/panduan/hapus-akun', [PanduanController::class, 'hapusAkun'])->name('panduan.hapus-akun');
// Protected routes
Route::middleware(['mitra.auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', [Member_DashboardController::class, 'index'])->name('member.dashboard');

    // Program routes
    Route::prefix('program')->group(function () {
        Route::get('/', [Member_ProgramController::class, 'index'])->name('member.program');
        Route::get('/{code}', [Member_ProgramController::class, 'show'])->name('member.program.show');
    });

    // Mitra routes
    Route::prefix('mitra')->group(function () {
        Route::get('/pendaftaran', [Member_MitraController::class, 'index'])->name('mitra.registration');
        Route::get('/mitra/{mitra}', [Member_MitraController::class, 'show'])->name('mitra.show');
        Route::get('/list', [Member_MitraController::class, 'list'])->name('mitra.list');
        Route::get('/genealogy', [Member_MitraController::class, 'genealogy'])->name('mitra.genealogy');
        Route::post('/store', [Member_MitraController::class, 'store'])->name('mitra.store');
        Route::get('/get-parent-mitra', [Member_MitraController::class, 'getParentMitra'])->name('mitra.getParentMitra');
    });

    // Customer routes
    Route::prefix('customers')->group(function () {
        Route::get('/pendaftaran', [Member_CustomerController::class, 'create'])->name('customer.create');
        Route::get('/list', [Member_CustomerController::class, 'list'])->name('customer.list');
        Route::get('/{customer}', [Member_CustomerController::class, 'show'])->name('customer.show');
        Route::post('/store', [Member_CustomerController::class, 'store'])->name('customer.store');
        Route::get('/edit/{id}', [Member_CustomerController::class, 'edit'])->name('customer.edit');
        Route::put('/update/{id}', [Member_CustomerController::class, 'update'])->name('customer.update');
        Route::delete('/delete/{id}', [Member_CustomerController::class, 'destroy'])->name('customer.destroy');
        Route::get('/{code}/payments', [Member_CustomerController::class, 'getPayments'])->name('customer.payments');
    });

    // Jamaah routes
    Route::prefix('jamaah')->group(function () {
        Route::get('/list', [Member_JamaahController::class, 'list'])->name('jamaah.list');
        Route::get('/{id}', [Member_JamaahController::class, 'show'])->name('jamaah.show');
    });

    // Bonus routes
    Route::prefix('bonus')->group(function () {
        Route::get('/', [Member_BonusController::class, 'index'])->name('bonus.index');
        Route::get('/history', [Member_BonusController::class, 'history'])->name('bonus.history');
    });

    // Tools Marketing routes
    Route::prefix('tools-marketing')->group(function () {
        Route::get('e-flayer', [Member_MarketingToolsController::class, 'eFlayer'])->name('tools-marketing.e-flayer');
        Route::get('video-promosi', [Member_MarketingToolsController::class, 'videoPromosi'])->name('tools-marketing.video-promosi');
    });

    // Akun routes
    // Rute Akun
    Route::prefix('account')->group(function () {
        Route::get('settings', [Member_AccountController::class, 'settings'])->name('account.settings');
        Route::get('info', [Member_AccountController::class, 'info'])->name('account.info');
        Route::post('update-profile', [Member_AccountController::class, 'updateProfile'])->name('account.update-profile');
        Route::post('update-profile-picture', [Member_AccountController::class, 'updateProfilePicture'])->name('account.update-profile-picture');
        Route::get('bank', [Member_AccountController::class, 'getBankAccount'])->name('account.bank');
        Route::post('update-bank', [Member_AccountController::class, 'updateBankAccount'])->name('account.update-bank');
        Route::post('update-password', [Member_AccountController::class, 'updatePassword'])->name('account.update-password');
        Route::post('deactivate', [Member_AccountController::class, 'deactivateAccount'])->name('account.deactivate');
        Route::get('edit-bank', [Member_AccountController::class, 'editBankAccount'])->name('account.edit-bank');
        Route::post('update-bank', [Member_AccountController::class, 'updateBankAccount'])->name('account.update-bank');
        Route::get('ganti-password', [Member_AccountController::class, 'updatePasswordIndex'])->name('account.security');
        Route::post('update-password', [Member_AccountController::class, 'updatePassword'])->name('account.update-password');
    });


    Route::post('/logout', [Member_AuthController::class, 'logout'])->name('mitra.logout');

});