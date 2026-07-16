<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{
    public function SubCategoryRecords()
    {
        $subCategories = SubCategory::with('category')->get();
        $categories = Category::all();
        return view('admin.view_sub_category_records', compact('subCategories', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'sub_category_name' => $request->sub_category_name,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sub_categories', 'public');
        }

        SubCategory::create($data);

        return redirect()->route('sub.category.records')->with('success', 'Sub Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'sub_category_name' => $request->sub_category_name,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            if ($subCategory->image) {
                Storage::disk('public')->delete($subCategory->image);
            }
            $data['image'] = $request->file('image')->store('sub_categories', 'public');
        }

        $subCategory->update($data);

        return redirect()->route('sub.category.records')->with('success', 'Sub Category updated successfully.');
    }

    public function toggleStatus($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->status = $subCategory->status === 'active' ? 'inactive' : 'active';
        $subCategory->save();

        return redirect()->route('sub.category.records')->with('success', 'Status updated successfully.');
    }

    public function destroy($id)
    {
        $subCategory = SubCategory::findOrFail($id);

        if ($subCategory->image) {
            Storage::disk('public')->delete($subCategory->image);
        }

        $subCategory->delete();

        return redirect()->route('sub.category.records')->with('success', 'Sub Category deleted successfully.');
    }
}
