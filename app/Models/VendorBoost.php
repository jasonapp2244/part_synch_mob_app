<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VendorBoost extends Model {
    use HasFactory;
    protected $fillable = ['vendor_id','package_id','boost_position_id','amount','currency','start_date','end_date','payment_status','payment_method','transaction_id','is_active','metadata'];
    protected $casts = ['metadata'=>'array','start_date'=>'datetime','end_date'=>'datetime'];
    public function vendor(){ return $this->belongsTo(User::class,'vendor_id'); }
    public function package(){ return $this->belongsTo(BoostPackage::class,'package_id'); }
    public function position(){ return $this->belongsTo(BoostPosition::class,'boost_position_id'); }
    // public function boostedProducts(){ return $this->hasMany(BoostedProduct::class,'vendor_boost_id'); }
}

