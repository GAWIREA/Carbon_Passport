<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        return view('seller.dashboard', ['user' => Auth::user()]);
    }

    public function catalog(): View
    {
        return view('seller.catalog', ['user' => Auth::user()]);
    }

    public function orders(): View
    {
        return view('seller.orders', ['user' => Auth::user()]);
    }
}
