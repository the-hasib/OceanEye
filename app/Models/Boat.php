<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boat extends Model
{
    use HasFactory;

    // 1. Allow Mass Assignment (Insert Data)
    // SQL Context: These are the columns we can insert data into using INSERT INTO boats (...)
    protected $fillable = [
        'user_id',
        'boat_name',
        'registration_number',
        'boat_type',
        'capacity',
    ];

    // 2. Define Relationship: Boat belongs to a User (Fisherman)
    // SQL Query Context: SELECT * FROM users WHERE id = boats.user_id
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
