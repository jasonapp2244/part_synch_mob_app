<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function show($id = null)
    {
        if (!$id || !is_numeric($id)) {
            return response()->json([
                'status'  => false,
                'message' => "Invalid or missing sub-category ID."
            ], 400);
        }

        $companies = DB::table('companies')
            ->where('sub_category_id', $id)
            ->where('status', 'active')
            ->get();

        if ($companies->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => "No companies found."
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => "Companies retrieved successfully.",
            'data'    => $companies
        ], 200);
    }
}
