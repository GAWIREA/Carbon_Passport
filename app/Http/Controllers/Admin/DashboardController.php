<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', ['user' => Auth::user()]);
    }

    public function users(): View
    {
        return view('admin.users', ['user' => Auth::user()]);
    }

    public function cms(): View
    {
        return view('admin.cms', ['user' => Auth::user()]);
    }
}
