<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function UserRecords()
    {
        $users = User::where('role_id', 3)->get();
        return view('admin.view_users_records', compact('users'));
    }
}
