<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Payments;

class CardDetails extends Model
{
    use HasFactory;

    // Specify the table if it differs from the default 'card_details'
    protected $table = 'card_details';

    // Specify the fillable fields (attributes) for mass assignment
    protected $fillable = [
        'user_id',          // Foreign key to link with the users table
        'card_number',      // Encrypted card number
        'card_holder_name', // Name on the card
        'expiry_month',     // Card expiration month
        'expiry_year',      // Card expiration year
        'cvv',              // Encrypted CVV (or consider storing a hash or token)
        'billing_address',  // Billing address
    ];

    // Optionally, specify hidden fields
    protected $hidden = [
        'card_number',
        'cvv',
    ];

    // Define relationships

    /**
     * Get the user that owns the card details.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mutators to encrypt/decrypt sensitive data

    /**
     * Encrypt the card number before saving it to the database.
     */
    // public function setCardNumberAttribute($value)
    // {
    //     $this->attributes['card_number'] = encrypt($value);
    // }

    // /**
    //  * Decrypt the card number when retrieving it from the database.
    //  */
    // public function getCardNumberAttribute($value)
    // {
    //     return decrypt($value);
    // }

    // /**
    //  * Encrypt the CVV before saving it to the database.
    //  */
    // public function setCvvAttribute($value)
    // {
    //     $this->attributes['cvv'] = encrypt($value);
    // }

    // /**
    //  * Decrypt the CVV when retrieving it from the database.
    //  */
    // public function getCvvAttribute($value)
    // {
    //     return decrypt($value);
    // }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'card_id', 'id');
    }
}
