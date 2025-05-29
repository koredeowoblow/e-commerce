<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Admin;
use App\Models\Product;
use App\Models\Role;
use App\Models\Vendor;

class DashboardController extends Controller
{
    //
    public function index()
    {
        // User statistics
        Auth::guard('api-admin')->user();
        $totalUsers = User::count();
        $verifiedUsers = User::where('verified_status', 'verified')->count();
        $unverifiedUsers = User::where('verified_status', 'unverified')->count();

        // Vendor statistics
        $totalVendors = Vendor::count();
        $verifiedVendors = Vendor::where('is_verified', true)->count();

        // Product and order statistics
        $totalProducts = Product::count();
        // $totalOrders = Order::count();
        // $pendingOrders = Order::where('status', 'pending')->count();
        // $totalEarnings = Order::where('status', 'paid')->sum('total_price');

        // return view('admin.dashboard', compact(
        //     'totalUsers', 'verifiedUsers', 'unverifiedUsers',
        //     'totalVendors', 'verifiedVendors',
        //     'totalProducts', 'totalOrders', 'pendingOrders', 'totalEarnings'
        // ));

        $allresult[] = $totalUsers;
        $allresult[] = $unverifiedUsers;
        $allresult[] = $totalVendors;
        $allresult[] = $totalProducts;
        $allresult[] = $verifiedUsers;

        return response()->json($allresult);
    }

    public function view()
    {
  Auth::guard('api-admin')->user();

        //$admin = Auth::guard('admin')->user();
        $admin = Admin::first();
        $title = "dashboard";
        return view('admins.dashboard', compact('admin', 'title'));
    }
    // public function getproduct()
    // {
    //     $Product = Product::withCount('products')->get();
    //     return response()->json($Product);
    // }
    // public function getUser()
    // {
    //     $user = User::withCount('users')->get();
    //     return response()->json($user);
    // }
    // public function getVendor(){
    //     $vendor= Vendor::withCount('vendors')->get();
    //     return response()->json($vendor);
    // }

}
