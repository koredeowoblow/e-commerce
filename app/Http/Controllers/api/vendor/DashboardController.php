<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    /**
     * Display the vendor dashboard.
     */
    public function index()
    {
        $vendor = Auth::guard('vendor')->user();

        // Fetch vendor-related data
        $productsCount = Product::where('vendor_id', $vendor->id)->count();
        // $ordersCount = Order::where('vendor_id', $vendor->id)->count();
        // $notifications = Notification::where('vendor_id', $vendor->id)->latest()->take(5)->get();

        // // Fetch earnings, assuming a simple sum of paid orders
        // $earnings = Order::where('vendor_id', $vendor->id)
        //     ->where('status', 'paid')
        //     ->sum('total_price');

        return response()->json('productsCount');
    }

    /**
     * Manage products.
     */
    public function products()
    {
        $vendor = Auth::guard('vendor')->user();
        $products = Product::where('vendor_id', $vendor->id)->paginate(10);

        return response()->json($products);
    }

    /**
     * View specific product.
     */
    public function showProduct($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $product = Product::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();

        return response()->json($product);
    }

    /**
     * Manage orders.
     */
    // public function orders()
    // {
    //     $vendor = Auth::guard('vendor')->user();
    //     $orders = Order::where('vendor_id', $vendor->id)->latest()->paginate(10);

    //     return view('vendor.orders.index', compact('orders'));
    // }

    // /**
    //  * View specific order details.
    //  */
    // public function showOrder($id)
    // {
    //     $vendor = Auth::guard('vendor')->user();
    //     $order = Order::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();

    //     return view('vendor.orders.show', compact('order'));
    // }

    // /**
    //  * Notifications page.
    //  */
    // public function notifications()
    // {
    //     $vendor = Auth::guard('vendor')->user();
    //     $notifications = Notification::where('vendor_id', $vendor->id)->paginate(10);

    //     return view('vendor.notifications.index', compact('notifications'));
    // }

    /**
     * Update account settings.
     */
    public function accountSettings()
    {
        $vendor = Auth::guard('vendor')->user();

        return view('vendor.settings.account', compact('vendor'));
    }

    // /**
    //  * Update account information.
    //  */
    // public function updateAccount(Request $request)
    // {
    //     $vendor = Auth::guard('vendor')->user();

    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:users,email,' . $vendor->id,
    //         'password' => 'nullable|string|min:8|confirmed',
    //     ]);

    //     $vendor->name = $request->name;
    //     $vendor->email = $request->email;

    //     if ($request->filled('password')) {
    //         $vendor->password = bcrypt($request->password);
    //     }

    //     $vendor->save();

    //     return redirect()->route('vendor.settings.account')->with('success', 'Account updated successfully!');
    // }

}
