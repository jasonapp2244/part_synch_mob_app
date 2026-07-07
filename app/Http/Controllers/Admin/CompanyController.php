<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function CompanyRecords()
    {
        $companies = Company::with('user')->get();
        return view('admin.view_company_records', compact('companies'));
    }
}
