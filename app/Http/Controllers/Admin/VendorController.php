<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function VendorRecords()
    {
        $vendors = User::where('role_id', 2)->get();
        return view('admin.view_vendor_records', compact('vendors'));
    }

    public function toggleStatus($id)
    {
        $vendor = User::where('role_id', 2)->findOrFail($id);
        $vendor->status = $vendor->status === 'active' ? 'inactive' : 'active';
        $vendor->save();

        return redirect()->route('vendor.records')->with('success', 'Vendor status updated to ' . $vendor->status . '.');
    }

    public function destroy($id)
    {
        $vendor = User::where('role_id', 2)->findOrFail($id);
        $vendor->delete();

        return redirect()->route('vendor.records')->with('success', 'Vendor deleted successfully.');
    }
}
