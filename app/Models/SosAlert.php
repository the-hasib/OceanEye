<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosAlert extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'location', 'status'];

    // Relationship: An SOS belongs to a Fisherman (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
