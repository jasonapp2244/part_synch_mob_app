<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function ProductRecords()
    {
        $products = Product::with('user')->get();
        return view('admin.view_product_records', compact('products'));
    }
}
