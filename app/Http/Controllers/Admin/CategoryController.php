<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function CategoryRecords()
    {
        $categories = Category::withCount('subCategories')->get();
        return view('admin.view_category_records', compact('categories'));
    }
}
