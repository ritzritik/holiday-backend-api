<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOptions extends Model
{
    use HasFactory;

    protected $table = 'payment_options';

    protected $fillable = [
        'id',
        'payment_mode'
    ];

}
