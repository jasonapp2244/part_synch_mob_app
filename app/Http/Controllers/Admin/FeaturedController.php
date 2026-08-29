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

        // Return to whichever screen the toggle was pressed on. This used to
        // always redirect to the Featured screen, so featuring a product from
        // Manage Products bounced the admin out of the list they were working
        // through.
        return back()->with('success', "Product {$status} successfully.");
    }
}
