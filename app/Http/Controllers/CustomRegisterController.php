<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomRegisterController extends Controller
{
    // Show the registration form
    public function showForm()
    {
        return view('register');
    }

    // Handle form submission
    public function register(Request $request)
    {
        // 1. Basic Validation
        $request->validate([
            'role' => 'required|in:fisherman,coast_guard',
            'password' => 'required|min:6',
        ]);

        // Custom Error Messages for duplicate accounts
        $messages = [
            'mobile.unique' => 'You already have an account, please login.',
            'license_no.unique' => 'You already have an account, please login.',
            'email.unique' => 'You already have an account, please login.',
            'service_id.unique' => 'You already have an account, please login.',
        ];

        // 2. Create User Instance
        $user = new User();
        $user->role = $request->role;
        $user->password = Hash::make($request->password);

        // 3. Role Specific Logic
        if ($request->role == 'fisherman') {

            // Validate Fisherman inputs
            $request->validate([
                'name' => 'required',
                'mobile' => 'required|unique:users,mobile',
                'license_no' => 'required|unique:users,license_no',
            ], $messages);

            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->license_no = $request->license_no;
            $user->nid = $request->nid;
            $user->address = $request->address;

            // Create dummy email for login logic
            $user->email = $request->mobile . '@oceaneye.local';

        } else {
            // Validate Coast Guard inputs
            $request->validate([
                'officer_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'service_id' => 'required|unique:users,service_id',
            ], $messages);

            $user->name = $request->officer_name;
            $user->email = $request->email;
            $user->service_id = $request->service_id;
            $user->station_zone = $request->station_zone;
        }

        // 4. Save to Database
        $user->save();

        // 5. Redirect to Login Page with Success Message
        // We use 'with' to flash a session message
        return redirect()->route('login')->with('success', 'Registration successful! Your account is Pending. Please login after 2 minutes.');
    }
}
