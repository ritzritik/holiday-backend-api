<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'premium_amount',
        'coverage_details',
        'duration',
        'active',
        'expiry_date',
    ];

    protected $casts = [
        'expiry_date' => 'datetime', // Cast to a Carbon instance
    ];
}
