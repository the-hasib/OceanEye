<?php

namespace App\Http\Controllers;

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
                'location' => '21.9N, 89.9E', // Fake GPS for now (Later we will get real GPS)
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
}
