<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{



    protected $table = 'products';
    protected $fillable = [
        'user_id',
        'vendor_id',
        'category_id',
        'sub_category_id',
        'company_id',
        'company_product_categories_id',
        'service_status',
        'service_type',
        'sku',
        'barcode',
        'warranty',
        'name',
        'description',
        'price',
        'product_pic',
        'min_order_quantity',
        'max_order_quantity',
        'modal_number',
        'expire_date',
        'discount_type',
        'size_options',
        'tags',
        'return_policy',
        'installation_required',
        'installation_guide_url',
        'stock_quantity',
        'discount',
        'brand',
        'weight',
        'dimensions',
        'is_active',
        'tax_rate',
        'status',
        'is_top',
        'top_start_date',
        'top_expire_date'
    ];

    // **Service  Type Enum**
    const SERVICE_TYPES = [
        'free' => 'Free',
        'paid' => 'Paid'
    ];
    // **Service Status Type Enum**
    const SERVICE_STATUS = [
        'No' => 'N',
        'Yes' => 'Y'
    ];

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    // public function company()
    // {
    //     return $this->belongsTo(Company::class);
    // }


    public function company()
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function companyLinks()
    {
        return $this->hasMany(CompanyProductCategoryLinks::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id', 'id')
            ->where('type', 'product');
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }



  

    public function companies()
    {
        return $this->belongsToMany(
            \App\Models\Company::class,
            'company_product_category_links',
            'product_id',
            'company_id'
        );
    }

    public function boostedProducts()
    {
        return $this->hasMany(BoostedProduct::class, 'product_id');
    }

    public function vendorBoosts()
    {
        return $this->belongsToMany(VendorBoost::class, 'boosted_products', 'product_id', 'vendor_boost_id')
            ->withPivot('boost_start', 'boost_end', 'is_active')
            ->withTimestamps();
    }

    public function activeBoosts()
    {
        return $this->hasMany(BoostedProduct::class, 'product_id')
            ->where('is_active', true)
            ->where('boost_end', '>', now());
    }
}
