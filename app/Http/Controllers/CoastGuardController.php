<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SosAlert;
use Illuminate\Support\Facades\Auth;

class CoastGuardController extends Controller
{
    public function index()
    {
        // --- SECURITY CHECK (Easy Way) ---
        // If user is NOT a coast_guard, stop them here.
        if (Auth::user()->role !== 'coast_guard') {
            abort(403, 'Unauthorized Access'); // Show Error Page
        }

        // 1. Get active alerts
        $active_alerts = SosAlert::where('status', 'active')
            ->with('user')
            ->latest()
            ->get();

        // 2. Count missions
        $mission_count = SosAlert::where('resolved_by', Auth::id())->count();

        return view('coast_guard', compact('active_alerts', 'mission_count'));
    }

    public function resolve($id)
    {
        // Security Check here too
        if (Auth::user()->role !== 'coast_guard') {
            abort(403, 'Unauthorized Access');
        }

        $alert = SosAlert::findOrFail($id);
        $alert->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id()
        ]);

        return back()->with('success', 'Mission Accomplished!');
    }
}
