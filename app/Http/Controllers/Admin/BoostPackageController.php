<?php

namespace App\Http\Controllers\Admin;

use App\Models\BoostPackage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BoostPackageController extends Controller
{
    public function index()
    {
        $packages = BoostPackage::where('status', 1)->get();
        return response()->json($packages);
    }
}
