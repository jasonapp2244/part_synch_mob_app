<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class FeaturedController extends Controller
{
    public function FeaturedRecords()
    {
        $products = Product::where('is_top', 1)->with('user')->get();
        return view('admin.view_featured_records', compact('products'));
    }
}
