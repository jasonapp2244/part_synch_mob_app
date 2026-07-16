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

    public function toggleFeatured($id)
    {
        $product = Product::findOrFail($id);
        $product->is_top = $product->is_top ? 0 : 1;
        $product->save();

        $status = $product->is_top ? 'featured' : 'unfeatured';
        return redirect()->route('featured.records')->with('success', "Product {$status} successfully.");
    }
}
