<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function SubCategoryRecords()
    {
        $subCategories = SubCategory::with('category')->get();
        return view('admin.view_sub_category_records', compact('subCategories'));
    }
}
