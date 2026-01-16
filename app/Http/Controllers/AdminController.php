<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Load dashboard with pending users
    public function index()
    {
        // SQL Query: SELECT * FROM users WHERE status = 'pending';
        // Fetch only users where status is 'pending'
        $pending_users = User::where('status', 'pending')->get();

        return view('admin', compact('pending_users'));
    }

    // 2. Approve a user
    public function approve($id)
    {
        // SQL Query: UPDATE users SET status = 'approved' WHERE id = [ID];
        $user = User::find($id);

        if ($user) {
            $user->status = 'approved';
            $user->save();
        }
        return back()->with('success', 'User Approved Successfully!');
    }

    // 3. Reject (delete) a pending user
    public function reject($id)
    {
        // SQL Query: DELETE FROM users WHERE id = [ID];
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return back()->with('error', 'User Rejected and Deleted.');
    }

    // --- NEW FUNCTIONS ADDED BELOW ---

    // 4. Show All Approved Users (For the 'Users' Page)
    public function allUsers()
    {
        // SQL Query: SELECT * FROM users WHERE role != 'admin' AND status = 'approved';
        $users = User::where('role', '!=', 'admin')
            ->where('status', 'approved')
            ->get();

        // This loads the 'admin_users.blade.php' file
        return view('admin_users', compact('users'));
    }

    // 5. Delete/Ban an active user from the User List
    public function deleteUser($id)
    {
        // SQL Query: DELETE FROM users WHERE id = [ID];
        $user = User::find($id);
        if($user){
            $user->delete();
            return back()->with('success', 'User has been banned/removed.');
        }
        return back()->with('error', 'User not found.');
    }
    // --- BOATS MANAGEMENT ---

    // 6. Show All Boats with Owner Name
    public function allBoats()
    {
        // SQL Query: SELECT * FROM boats JOIN users ON boats.user_id = users.id;
        // 'with(user)'
        $boats = \App\Models\Boat::with('user')->get();

        return view('admin_boats', compact('boats'));
    }

    // 7. Delete a Boat (Admin Power)
    public function deleteBoat($id)
    {
        $boat = \App\Models\Boat::find($id);
        if($boat){
            $boat->delete();
            return back()->with('success', 'Boat removed permanently.');
        }
        return back()->with('error', 'Boat not found.');
    }
    // 8. Show Live Map
// 8. Show Live Map (Updated)
    public function map()
    {
        // Get active SOS alerts (Red Markers)
        $alerts = \App\Models\SosAlert::where('status', 'active')->with('user')->get();

        // Get ALL registered boats (Blue Markers)
        $boats = \App\Models\Boat::with('user')->get();

        return view('admin_map', compact('alerts', 'boats'));
    }
}
