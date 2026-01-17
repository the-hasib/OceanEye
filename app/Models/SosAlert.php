<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosAlert extends Model
{
    use HasFactory;

    // Added 'resolved_by' to allow saving the rescuer's ID
    protected $fillable = ['user_id', 'location', 'status', 'resolved_by'];

    // Relationship: The fisherman who sent the SOS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: The Coast Guard who resolved/rescued the alert
    public function rescuer()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
