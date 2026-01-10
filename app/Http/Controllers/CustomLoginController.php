<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    // Handle the login logic
    public function login(Request $request)
    {
        // 1. Validate Input
        $request->validate([
            'email' => 'required', // Can be Mobile (Fisherman) or Email (Coast Guard)
            'password' => 'required',
        ]);

        // 2. Prepare Credentials based on Input Type
        $input = $request->email;
        $credentials = [];

        if (is_numeric($input)) {
            // Logic for Fisherman (Mobile Number)
            // Convert mobile to the dummy email format used in registration
            $credentials['email'] = $input . '@oceaneye.local';
            $credentials['password'] = $request->password;
        } else {
            // Logic for Coast Guard (Standard Email)
            $credentials['email'] = $input;
            $credentials['password'] = $request->password;
        }

        // 3. Attempt Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // SUCCESS: Redirect to dashboard with Welcome Message
            return redirect()->route('dashboard')->with('success', 'Welcome to OceanEye');
        }

        // 4. FAILURE: Wrong Password or Not Registered
        return back()->withErrors([
            'login_error' => 'Invalid mobile/email or password. Please register if you are new.',
        ])->withInput(); // Keep the input field filled
    }
}
