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
}
