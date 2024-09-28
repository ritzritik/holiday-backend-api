<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactAdmin extends Model
{
    use HasFactory;

    // Define the fillable fields
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'message',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
