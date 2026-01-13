<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\CustomLoginController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

// Public Routes
Route::get('/', function () { return view('welcome'); });

// Registration Routes
Route::get('/register', [CustomRegisterController::class, 'showForm'])->name('register');
Route::post('/register', [CustomRegisterController::class, 'register']);

// --- LOGIN ROUTES FIXED ---
Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
// Added 'login.post' name here so the HTML form can find it
Route::post('/login', [CustomLoginController::class, 'login'])->name('login.post');

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Protected Routes (Login Required)
Route::middleware(['auth'])->group(function () {

    // 1. Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Admin Actions
    Route::get('/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::get('/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    // 2. Coast Guard Dashboard
    Route::get('/coast-guard/dashboard', function () {
        return view('coast_guard');
    })->name('coast_guard.dashboard');

    // 3. Fisherman Dashboard
    Route::get('/fisherman/dashboard', function () {
        return view('fisherman');
    })->name('fisherman.dashboard');

    // 4. Main Dashboard Redirect
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($role == 'coast_guard') {
            return redirect()->route('coast_guard.dashboard');
        }
        return redirect()->route('fisherman.dashboard');
    })->name('dashboard');

});
