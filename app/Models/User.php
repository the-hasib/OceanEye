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
        'role',
        'status',
        'mobile',
        'license_no',
        'nid',
        'address',
        'service_id',
        'station_zone',
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
    /**
     * Relationship: A User (Fisherman) can have multiple Boats.
     */
    public function boats()
    {
        return $this->hasMany(Boat::class);
    }

}
