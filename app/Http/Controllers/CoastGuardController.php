<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SosAlert;
use App\Models\Boat;
use Illuminate\Support\Facades\Auth;

class CoastGuardController extends Controller
{
    public function index()
    {
        // 1. Security Check
        if (Auth::user()->role !== 'coast_guard') {
            abort(403, 'Unauthorized Access');
        }

        // 2. Active SOS Alerts
        $active_alerts = SosAlert::where('status', 'active')
            ->with('user')
            ->latest()
            ->get();

        // 3. Mission Count
        $mission_count = SosAlert::where('resolved_by', Auth::id())->count();

        // 4. Get All Boats
        $boats = Boat::all();

        return view('coast_guard', compact('active_alerts', 'mission_count', 'boats'));
    }

    public function resolve($id)
    {
        if (Auth::user()->role !== 'coast_guard') {
            abort(403, 'Unauthorized Access');
        }

        $alert = SosAlert::findOrFail($id);
        $alert->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id()
        ]);

        return back()->with('success', 'Mission Completed!');
    }
}
