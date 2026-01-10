<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Add ALL your custom database columns here.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',          // <--- Added
        'mobile',        // <--- Added
        'license_no',    // <--- Added
        'nid',           // <--- Added
        'address',       // <--- Added
        'service_id',    // <--- Added
        'station_zone',  // <--- Added
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
