<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'username',
        'bio',
        'profile_photo',
        'is_active',
        'password',
        'is_deleted',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user that created this user.
     */
    public function creator() // optional
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * JWTSubject methods required by tymon/jwt-auth
     */

    // Get the identifier that will be stored in the subject claim of the JWT
    public function getJWTIdentifier()
    {
        return $this->getKey(); // This returns the primary key of the user (usually id)
    }

    // Return a key-value array, containing any custom claims to be added to the JWT
    public function getJWTCustomClaims()
    {
        return []; // You can add custom claims here if necessary
    }
}
