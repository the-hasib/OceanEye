<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Boat;
use App\Models\SosAlert; //
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // 1. Admin Dashboard (Main Page)
    public function index()
    {
        // --- COUNTS (Stat Cards) ---
        $total_users = User::count();
        $pending_count = User::where('status', 'pending')->count();
        $active_sos = SosAlert::where('status', 'active')->count(); // Active SOS Count
        $coast_guard_count = User::where('role', 'coast_guard')->count();

        // --- LISTS (Tables) ---

        // 1. Pending Users List (For Approval Table)
        $pending_users = User::where('status', 'pending')->get();

        // 2. Active SOS List (For Dashboard Warning Table)
        $sos_alerts = SosAlert::where('status', 'active')
            ->with(['user', 'boat'])
            ->latest()
            ->get();

        // Pass all variables to the view
        return view('admin', compact(
            'total_users',
            'pending_count',
            'active_sos',
            'coast_guard_count',
            'pending_users',
            'sos_alerts'
        ));
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

    // 3. Reject (delete) a pending user
    public function reject($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return back()->with('error', 'User Rejected and Deleted.');
    }

    // 4. Show All Approved Users
    public function allUsers()
    {
        $users = User::where('role', '!=', 'admin')
            ->where('status', 'approved')
            ->get();

        return view('admin_users', compact('users'));
    }

    // 5. Delete/Ban an active user
    public function deleteUser($id)
    {
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
        $boats = Boat::with('user')->get();
        return view('admin_boats', compact('boats'));
    }

    // 7. Delete a Boat
    public function deleteBoat($id)
    {
        $boat = Boat::find($id);
        if($boat){
            $boat->delete();
            return back()->with('success', 'Boat removed permanently.');
        }
        return back()->with('error', 'Boat not found.');
    }

    // 8. Show Live Map
    public function map()
    {
        // Get active SOS alerts (Red Markers) with User Info
        $alerts = SosAlert::where('status', 'active')->with('user')->get();

        // Get ALL registered boats (Blue Markers) with User Info
        $boats = Boat::with('user')->get();

        return view('admin_map', compact('alerts', 'boats'));
    }

    // --- ANALYTICS & REPORTS ---
    public function analytics()
    {
        // 1. Boat Stats
        $boat_stats = DB::table('boats')
            ->select('boat_type', DB::raw('count(*) as total'))
            ->groupBy('boat_type')
            ->get();

        // 2. Top Fishermen
        $top_fishermen = User::where('role', 'fisherman')
            ->withCount('boats')
            ->orderBy('boats_count', 'desc')
            ->take(5)
            ->get();

        // 3. Monthly SOS Report
        $monthly_sos = DB::table('sos_alerts')
            ->select(DB::raw('MONTHNAME(created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->get();

        // 4. Inactive Users
        $inactive_users = User::where('role', 'fisherman')
            ->doesntHave('boats')
            ->get();

        // 5. Top Rescue Units
        $top_rescuers = DB::table('users')
            ->join('sos_alerts', 'users.id', '=', 'sos_alerts.resolved_by')
            ->select('users.name', DB::raw('count(sos_alerts.id) as total_rescues'))
            ->groupBy('users.name')
            ->orderBy('total_rescues', 'desc')
            ->get();

        return view('admin_analytics', compact('boat_stats', 'top_fishermen', 'monthly_sos', 'inactive_users', 'top_rescuers'));
    }
}
