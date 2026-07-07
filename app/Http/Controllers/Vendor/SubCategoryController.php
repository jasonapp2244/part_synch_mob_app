<?php

namespace App\Http\Controllers\Vendor;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SubCategoryController extends Controller
{
    public function show($categoryId = null)
    {
        if (!$categoryId || !is_numeric($categoryId)) {
            return response()->json([
                'status'  => false,
                'message' => "Invalid or missing category ID."
            ], 400);
        }

        $subCategories = DB::table('sub_categories')
            ->where('category_id', $categoryId)
            ->where('status', 'active')
            ->get();

        if ($subCategories->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => "No subcategories found for this category."
            ], 400);
        }

        return response()->json([
            'status'  => true,
            'message' => "Subcategories retrieved successfully.",
            'data'    => $subCategories
        ], 200);
    }
}
