<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class ProductCategoryController extends Controller
{
    public function show($id = null)
    {
        if (!$id || !is_numeric($id)) {
            return response()->json([
                'status'  => false,
                'message' => "Invalid or missing company ID."
            ], 400);
        }

        $productCategories = DB::table('company_product_categories')
            ->where('company_id', $id)
            ->where('status', 'active')
            ->get();

        if ($productCategories->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => "No product categories found for this company."
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => "Product categories retrieved successfully.",
            'data'    => $productCategories
        ], 200);
    }
}
