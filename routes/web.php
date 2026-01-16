<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\CustomLoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BoatController;
use App\Http\Controllers\SosController; // Added SOS Controller
use Illuminate\Support\Facades\Auth;

// 1. Root Route -> Redirect to Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Registration Routes
Route::get('/register', [CustomRegisterController::class, 'showForm'])->name('register');
Route::post('/register', [CustomRegisterController::class, 'register'])->name('register.post');

// 3. Login Routes
Route::get('/login', [CustomLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [CustomLoginController::class, 'login'])->name('login.post');

// 4. Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// 5. Protected Routes (Login Required)
Route::middleware(['auth'])->group(function () {

    // --- Admin Routes ---
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // User Management
    Route::get('/admin/users', [AdminController::class, 'allUsers'])->name('admin.users');
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');
    Route::get('/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
    Route::get('/reject/{id}', [AdminController::class, 'reject'])->name('admin.reject');

    // Boats Management
    Route::get('/admin/boats', [AdminController::class, 'allBoats'])->name('admin.boats');
    Route::delete('/admin/boats/delete/{id}', [AdminController::class, 'deleteBoat'])->name('admin.deleteBoat');

    // SOS Monitor (New Admin Route)
    Route::get('/admin/sos', [SosController::class, 'index'])->name('admin.sos');


    // --- Fisherman & Boat Routes ---
    Route::get('/fisherman/dashboard', [BoatController::class, 'index'])->name('fisherman.dashboard');
    Route::post('/boats/store', [BoatController::class, 'store'])->name('boats.store');
    Route::delete('/boats/delete/{id}', [BoatController::class, 'destroy'])->name('boats.delete');

    // SOS Send Signal (New Fisherman Route)
    Route::post('/sos/send', [SosController::class, 'store'])->name('sos.send');


    // --- Dashboard Views ---
    Route::get('/coast-guard/dashboard', function () {
        return view('coast_guard');
    })->name('coast_guard.dashboard');


    // --- Main Redirect Logic ---
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;

        if ($role == 'admin') return redirect()->route('admin.dashboard');
        if ($role == 'coast_guard') return redirect()->route('coast_guard.dashboard');

        return redirect()->route('fisherman.dashboard');
    })->name('dashboard');

});
