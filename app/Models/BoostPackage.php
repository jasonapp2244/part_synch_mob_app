<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoostPackage extends Model {
    use HasFactory;
    protected $fillable = ['name','slug','price','product_limit','duration_days','currency','status','description'];
    public function boosts(){ return $this->hasMany(VendorBoost::class,'package_id'); }
}
