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

        // 5. [NEW] Get Current Signal Status from Cache
        $current_signal = Cache::get('weather_signal', 0);

        //  $current_signal
        return view('coast_guard', compact('active_alerts', 'mission_count', 'boats', 'current_signal'));
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
        if (Auth::user()->role !== 'coast_guard') {
            abort(403, 'Unauthorized Access');
        }

        $signal = $request->input('signal');
        Cache::put('weather_signal', $signal, 1440);

        // [LOGIC FIXED]
        if ($signal == 0) {

            return back()->with('success', "✅ SIGNAL CLEARED: Sea is now declared Safe for all boats.");
        } else {

            return back()->with('warning', "⚠️ HIGH ALERT: Danger Signal #$signal has been broadcast to all boats!");
        }
    }
}
