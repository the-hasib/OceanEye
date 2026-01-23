<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SosAlert;
use App\Models\Boat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
// [NEW] Function to broadcast danger signal to all fishermen
    public function sendWarning(Request $request)
    {
        // 1. Security Check: Only Coast Guard can access
        if (Auth::user()->role !== 'coast_guard') {
            abort(403, 'Unauthorized Access');
        }

        // 2. Get the signal number (1-10) from the form input
        $signal = $request->input('signal');

        // 3. Store the signal in Cache memory for 24 hours (1440 mins)
        // This allows the Fisherman dashboard to read this value later
        Cache::put('weather_signal', $signal, 1440);

        // 4. Redirect back with a success message
        return back()->with('warning', "⚠️ HIGH ALERT: Danger Signal #$signal has been broadcast to all boats!");
    }
}
