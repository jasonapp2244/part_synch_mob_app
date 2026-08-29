<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'vendor_id',
        'rating',
        'title',
        'review_text',
        'review_type',
        'status',
        'verified',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /** The vendor being reviewed — a users row, same as orders.vendor_id. */
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
