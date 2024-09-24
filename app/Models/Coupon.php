<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $primaryKey = 'coupon_id';
    public $incrementing = false;
    protected $fillable = [
        'coupon_code', 'discount', 'active', 'expiry_date','is_deleted','is_expired', 'created_by', 'updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(AuthUser::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected $casts = [
        'expiry_date' => 'datetime',
    ];
}
