<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\SosAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SosController extends Controller
{
    // 1. Fisherman sends SOS
    public function store()
    {
        // Check if user already has an active SOS (Prevent duplicate spamming)
        $existing = SosAlert::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if(!$existing) {
            SosAlert::create([
                'user_id' => Auth::id(),
                'location' => '21.9N, 89.9E', // Fake GPS for now
                'status' => 'active'
            ]);
            return back()->with('success', 'SOS Signal Sent! Help is on the way!');
        }

        return back()->with('error', 'You already have an active SOS alert!');
    }

    // 2. Admin/Coast Guard views SOS List
    public function index()
    {
        // Get only ACTIVE alerts with User info
        $alerts = SosAlert::where('status', 'active')->with('user')->get();

        return view('admin_sos', compact('alerts'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'boat_id' => 'required', // after found boat id then send sos
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        SosAlert::create([
            'user_id' => Auth::id(),
            'boat_id' => $request->boat_id, // save boat id
            'status' => 'active',
            'latitude' => $request->latitude ?? 0.0000,
            'longitude' => $request->longitude ?? 0.0000,
        ]);

        return back()->with('success', 'SOS Sent! Coast Guard has been notified for your boat.');
    }
}
