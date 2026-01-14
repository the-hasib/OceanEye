<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Load dashboard with pending users
    public function index()
    {
        // Query
        // Fetch only users where status is 'pending'
        $pending_users = User::where('status', 'pending')->get();

        return view('admin', compact('pending_users'));
    }

    // 2. Approve a user
    public function approve($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->status = 'approved';
            $user->save();
        }
        return back()->with('success', 'User Approved Successfully!');
    }

    // 3. Reject (delete) a user
    public function reject($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return back()->with('error', 'User Rejected and Deleted.');
    }
}
