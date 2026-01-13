<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    // 1. Show the Login Page (New Function Added)
    public function showLoginForm()
    {
        return view('login');
    }

    // 2. Handle the login logic
    public function login(Request $request)
    {
        // Validate Input
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // Prepare Credentials
        $input = $request->email;
        $credentials = [];

        if (is_numeric($input)) {
            // Fisherman Logic (Mobile)
            $credentials['email'] = $input . '@oceaneye.local';
            $credentials['password'] = $request->password;
        } else {
            // Coast Guard Logic (Email)
            $credentials['email'] = $input;
            $credentials['password'] = $request->password;
        }

        // Attempt Login
        if (Auth::attempt($credentials)) {

            // Check Status
            $user = Auth::user();

            if ($user->status == 'pending') {
                // If pending, force logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'login_error' => 'Account Pending! Please wait for Admin approval.',
                ])->withInput();
            }

            // If Approved, Proceed
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Welcome back!');
        }

        // Failure
        return back()->withErrors([
            'login_error' => 'Invalid mobile/email or password.',
        ])->withInput();
    }
}
