<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = ['category_id', 'sub_category_name', 'image', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company(){
        return $this->hasMany(Company::class);
    }
}

