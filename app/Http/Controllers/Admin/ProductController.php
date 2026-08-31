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

    public function toggleStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status === 'active' ? 'inactive' : 'active';
        $product->save();

        return redirect()->route('product.records')->with('success', 'Product status updated to ' . $product->status . '.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('product.records')->with('success', 'Product deleted successfully.');
    }
}
