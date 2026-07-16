<?php

namespace App\Http\Controllers\Admin;

use App\Models\BoostPackage;
use App\Models\BoostPosition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class BoostPackageController extends Controller
{
    // API endpoint (existing)
    public function index()
    {
        $packages = BoostPackage::where('status', 1)->get();
        return response()->json($packages);
    }

    // Admin web view
    public function list()
    {
        $packages = BoostPackage::all();
        $positions = BoostPosition::all();
        return view('admin.view_boost_packages', compact('packages', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'product_limit' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        BoostPackage::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'product_limit' => $request->product_limit,
            'duration_days' => $request->duration_days,
            'currency' => $request->currency ?? 'usd',
            'status' => true,
            'description' => $request->description,
        ]);

        return redirect()->route('boost.packages')->with('success', 'Boost package created successfully.');
    }

    public function update(Request $request, $id)
    {
        $package = BoostPackage::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'product_limit' => 'required|integer|min:1',
            'duration_days' => 'required|integer|min:1',
            'currency' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);

        $package->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'product_limit' => $request->product_limit,
            'duration_days' => $request->duration_days,
            'currency' => $request->currency ?? 'usd',
            'description' => $request->description,
        ]);

        return redirect()->route('boost.packages')->with('success', 'Boost package updated successfully.');
    }

    public function toggleStatus($id)
    {
        $package = BoostPackage::findOrFail($id);
        $package->status = !$package->status;
        $package->save();

        return redirect()->route('boost.packages')->with('success', 'Package status updated.');
    }

    public function destroy($id)
    {
        $package = BoostPackage::findOrFail($id);
        $package->delete();

        return redirect()->route('boost.packages')->with('success', 'Boost package deleted.');
    }
}
