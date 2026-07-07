<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = DB::table('categories')->get();
        if ($categories->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => "No records found."
            ], 404);
        }

        return response()->json([
            'status'    => true,
            'message'   => "Categories retrieved successfully.",
            'data'      => $categories
        ], 200);
    }
}
