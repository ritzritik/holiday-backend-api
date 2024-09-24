<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CardDetails;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'amount',
        'is_accepted',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card details associated with the payment.
     */
    public function cardDetails()
    {
        return $this->belongsTo(CardDetails::class, 'card_id');
    }
}
