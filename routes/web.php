<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\CustomLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Root URL redirects to Login Page
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Registration Routes
Route::get('/register', [CustomRegisterController::class, 'showForm'])->name('register');
Route::post('/register', [CustomRegisterController::class, 'register'])->name('register.post');

// 3. Login Routes
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [CustomLoginController::class, 'login'])->name('login.post');

// 4. Dashboard Logic (Checks Role & Redirects)
Route::get('/dashboard', function () {

    // Redirect to login if not authenticated
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login to access the dashboard.');
    }

    $role = Auth::user()->role;

    // Load view based on user role
    if ($role == 'admin') {
        return view('admin');
    } elseif ($role == 'coast_guard') {
        return view('coast_guard');
    } else {
        return view('fisherman');
    }

})->name('dashboard')->middleware('auth');

// 5. Logout Route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login')->with('success', 'Logged out successfully.');
})->name('logout');
