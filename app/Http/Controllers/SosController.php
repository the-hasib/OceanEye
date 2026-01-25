<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SosAlert;
use Illuminate\Support\Facades\Auth;

class SosController extends Controller
{
    // 1. SOS Send Function (Fisherman sends this)
    public function send(Request $request)
    {
        // Validation
        $request->validate([
            'boat_id' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        // Fake GPS Logic (If browser fails)
        $randomLat = 21.50 + (mt_rand(0, 5000) / 10000);
        $randomLng = 89.00 + (mt_rand(0, 9000) / 10000);

        $finalLat = $request->latitude ? $request->latitude : $randomLat;
        $finalLng = $request->longitude ? $request->longitude : $randomLng;

        // Check if already active
        $existingAlert = SosAlert::where('user_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        if ($existingAlert) {
            return back()->with('error', 'Please wait! You already have an active SOS.');
        }

        // Save to Database
        SosAlert::create([
            'user_id'   => Auth::id(),
            'boat_id'   => $request->boat_id,
            'status'    => 'active',
            'latitude'  => $finalLat,
            'longitude' => $finalLng,
            'location'  => number_format($finalLat, 4) . ', ' . number_format($finalLng, 4),
        ]);

        return back()->with('success', 'SOS Signal Sent Successfully!');
    }

    // 2. Admin/Coast Guard Monitor Page
    public function index()
    {
        $alerts = SosAlert::where('status', 'active')
            ->with(['user', 'boat'])
            ->latest()
            ->get();


        if(view()->exists('admin_sos')) {
            return view('admin_sos', compact('alerts'));
        } else {
            return view('admin.sos', compact('alerts'));
        }
    }
}
