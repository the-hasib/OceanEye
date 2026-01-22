<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosAlert extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Updated to include boat information and precise GPS coordinates.
     */
    protected $fillable = [
        'user_id',
        'boat_id',      // Link to the specific boat in danger
        'latitude',     // GPS Latitude
        'longitude',    // GPS Longitude
        'location',     // General location name (optional/backup)
        'status',       // Status: active, resolved
        'resolved_by'   // ID of the Coast Guard who solved it
    ];

    /**
     * Relationship: Get the fisherman (User) who sent the SOS.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Get the boat involved in the emergency.
     */
    public function boat()
    {
        return $this->belongsTo(Boat::class);
    }

    /**
     * Relationship: Get the Coast Guard (User) who resolved the alert.
     */
    public function rescuer()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
