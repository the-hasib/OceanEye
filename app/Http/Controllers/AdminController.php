<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Load dashboard with pending users

    public function index()
    {
        // 1. Fetch Real Counts from Database
        $total_users = User::count();
        $pending_count = User::where('status', 'pending')->count();
        $active_sos = \App\Models\SosAlert::where('status', 'active')->count(); // Active SOS Count
        $coast_guard_count = User::where('role', 'coast_guard')->count();

        // SQL Query: SELECT * FROM users WHERE status = 'pending';
        // Fetch only users where status is 'pending'
        $pending_users = User::where('status', 'pending')->get();

        // 3. Pass all variables to the view
        return view('admin', compact('total_users', 'pending_count', 'active_sos', 'coast_guard_count', 'pending_users'));
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
    // --- ANALYTICS & REPORTS (COMPLEX QUERIES) ---
    public function analytics()
    {
        // 1. Boat Stats (Group By)
        $boat_stats = DB::table('boats')
            ->select('boat_type', DB::raw('count(*) as total'))
            ->groupBy('boat_type')
            ->get();

        // 2. Top Fishermen (Relationship Count)
        $top_fishermen = \App\Models\User::where('role', 'fisherman')
            ->withCount('boats')
            ->orderBy('boats_count', 'desc')
            ->take(5)
            ->get();

        // 3. Monthly SOS Report (Date Function)
        $monthly_sos = DB::table('sos_alerts')
            ->select(DB::raw('MONTHNAME(created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->get();

        // 4. Inactive Users (Subquery / DoesntHave)
        $inactive_users = \App\Models\User::where('role', 'fisherman')
            ->doesntHave('boats')
            ->get();

        // 5. Top Rescue Units (JOIN Query)
        $top_rescuers = DB::table('users')
            ->join('sos_alerts', 'users.id', '=', 'sos_alerts.resolved_by')
            ->select('users.name', DB::raw('count(sos_alerts.id) as total_rescues'))
            ->groupBy('users.name')
            ->orderBy('total_rescues', 'desc')
            ->get();

        return view('admin_analytics', compact('boat_stats', 'top_fishermen', 'monthly_sos', 'inactive_users', 'top_rescuers'));
    }
}
