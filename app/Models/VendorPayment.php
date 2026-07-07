<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
