<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoostedProduct extends Model
{
    use HasFactory;

    protected $table = 'boosted_products';
    
    protected $fillable = [
        'vendor_boost_id',
        'product_id',
        'boost_start',
        'boost_end',
        'is_active'
    ];

    protected $casts = [
        'boost_start' => 'datetime',
        'boost_end' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function vendorBoost()
    {
        return $this->belongsTo(VendorBoost::class, 'vendor_boost_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

