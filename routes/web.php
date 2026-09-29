<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CertificateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROUTE PUBLIK
// ==========================================
Route::get('/', function () {
    return view('pages.landing');
})->name('landing');

Route::get('/certificate/search', [CertificateController::class, 'showAll'])->name('certificate.search');
Route::get('/cek-sertifikat', [CertificateController::class, 'showAll'])->name('public.certificate.check');

// ==========================================
// ROUTE AUTHENTICATION
// ==========================================
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect()->route('admin.certificate.create');
    }

    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if (isset($user->is_active) && ! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan oleh Owner.',
            ]);
        }

        $request->session()->regenerate();
        session(['user_email' => $user->email]);

        return redirect()->intended(route('admin.certificate.create'));
    }

    return back()->withErrors([
        'email' => 'Email atau password yang Anda masukkan salah.',
    ])->onlyInput('email');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// ==========================================
// ROUTE ADMIN & OWNER (DIPROTEKSI MIDDLEWARE AUTH)
// ==========================================
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Kelola Sertifikat
    Route::get('/certificate/create', [CertificateController::class, 'create'])->name('certificate.create');
    Route::post('/certificate/store', [CertificateController::class, 'store'])->name('certificate.store');
    Route::get('/certificate/editor/{id}', [CertificateController::class, 'editor'])->name('certificate.editor');
    Route::post('/certificate/update-positions/{id}', [CertificateController::class, 'updatePositions'])->name('certificate.update_positions');

    // Route untuk Cek/Lihat Semua Sertifikat
    Route::get('/certificate/check', [CertificateController::class, 'showAll'])->name('certificate.show_all');
    Route::get('/certificate/index', [CertificateController::class, 'showAll'])->name('certificate.index');
    Route::get('/certificate/list', [CertificateController::class, 'showAll'])->name('certificate.check');

    Route::get('/certificate/download/{id}', [CertificateController::class, 'download'])->name('certificate.download');
    Route::delete('/certificate/delete/{id}', [CertificateController::class, 'destroy'])->name('certificate.destroy');

    // Fitur Khusus Owner & Admin
    Route::get('/certificate/history', [AdminUserController::class, 'history'])->name('certificate.history');
    Route::get('/user/index', [AdminUserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [AdminUserController::class, 'create'])->name('user.create');
    Route::post('/user/store', [AdminUserController::class, 'store'])->name('user.store');
    Route::patch('/user/toggle/{id}', [AdminUserController::class, 'toggleStatus'])->name('user.toggle');
});
