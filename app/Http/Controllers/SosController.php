<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SosAlert;
use Illuminate\Support\Facades\Auth;

class SosController extends Controller
{
    /**
     * Handle the SOS signal sent by a Fisherman.
     * It validates the request, generates a simulated location if real GPS is missing,
     * and saves the emergency alert to the database.
     */
    public function store(Request $request)
    {
        // 1. Validation: Ensure a boat is selected by the fisherman
        $request->validate([
            'boat_id' => 'required', // Mandatory: We need to know which boat is in danger
            'latitude' => 'nullable',
            'longitude' => 'nullable',
        ]);

        // 2. Location Logic: Handle missing GPS coordinates
        // If the browser fails to provide a location, we generate a random coordinate
        // within the Bay of Bengal / Sundarbans region to simulate the boat's position.
        // Coordinate Range: Lat 21.50 - 22.00, Lng 89.00 - 89.90

        $randomLat = 21.50 + (mt_rand(0, 5000) / 10000);
        $randomLng = 89.00 + (mt_rand(0, 9000) / 10000);

        // Determine final location: Use real GPS if available, otherwise use the random simulation
        $finalLat = $request->latitude ? $request->latitude : $randomLat;
        $finalLng = $request->longitude ? $request->longitude : $randomLng;

        // 3. Spam Prevention: Check if this user already has an active SOS
        // We do not want to spam the Coast Guard with duplicate alerts.
        $existingAlert = SosAlert::where('user_id', Auth::id())
            ->where('status', 'active')
            ->exists();

        if ($existingAlert) {
            return back()->with('error', 'Please wait! You already have an active SOS alert in progress.');
        }

        // 4. Save the SOS Alert to the Database
        SosAlert::create([
            'user_id'   => Auth::id(),        // The fisherman (Owner)
            'boat_id'   => $request->boat_id, // The specific boat details
            'status'    => 'active',          // Mark the alert as currently active
            'latitude'  => $finalLat,         // Saved Latitude
            'longitude' => $finalLng,         // Saved Longitude
        ]);

        return back()->with('success', 'SOS Signal Sent! The Coast Guard has received your boat\'s location.');
    }

    /**
     * Display the list of active SOS alerts to the Admin or Coast Guard.
     * Uses Eager Loading to fetch User and Boat details efficiently.
     */
    public function index()
    {
        // Fetch only 'active' alerts
        // 'with(['user', 'boat'])' ensures we get the Owner's Name and Boat Details
        $alerts = SosAlert::where('status', 'active')
            ->with(['user', 'boat'])
            ->latest() // Sort by newest first
            ->get();

        return view('admin_sos', compact('alerts'));
    }
}
