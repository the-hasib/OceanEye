<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Boat;

class BoatController extends Controller
{
    // 1. Show the list of boats for the logged-in Fisherman
    public function index()
    {
        // SQL Query: SELECT * FROM boats WHERE user_id = [CURRENT_USER_ID];
        $boats = Boat::where('user_id', Auth::id())->get();

        return view('fisherman', compact('boats'));
    }

    // 2. Store a new boat in the database
    public function store(Request $request)
    {
        // Validate Input
        // SQL Query (Behind the scenes): SELECT count(*) FROM boats WHERE registration_number = '...';
        $request->validate([
            'boat_name' => 'required',
            'registration_number' => 'required|unique:boats,registration_number',
            'boat_type' => 'required',
            'capacity' => 'required|integer',
        ]);

        // Create Boat linked to the logged-in User
        // SQL Query: INSERT INTO boats (user_id, boat_name, registration_number, boat_type, capacity, ...) VALUES (...);

        $boat = new Boat();
        $boat->user_id = Auth::id(); // Foreign Key Linking
        $boat->boat_name = $request->boat_name;
        $boat->registration_number = $request->registration_number;
        $boat->boat_type = $request->boat_type;
        $boat->capacity = $request->capacity;
        $boat->save();

        return back()->with('success', 'Boat Registered Successfully!');
    }

    // 3. Delete a boat
    public function destroy($id)
    {
        // SQL Query: DELETE FROM boats WHERE id = [ID] AND user_id = [CURRENT_USER_ID];
        $boat = Boat::where('id', $id)->where('user_id', Auth::id())->first();

        if ($boat) {
            $boat->delete();
            return back()->with('success', 'Boat Deleted Successfully.');
        }

        return back()->with('error', 'Boat not found or access denied.');
    }
}
