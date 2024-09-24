<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'text',
        'image',
        'status',
        'created_by',
    ];
    
    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }
}
