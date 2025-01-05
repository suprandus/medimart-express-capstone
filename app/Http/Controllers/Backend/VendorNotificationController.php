<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class VendorNotificationController extends Controller
{
    // Notification index page [vendor]
    public function index()
    {
        $totalPendingOrder = Order::where('order_status', 'pending')
            ->whereHas('orderProducts', function ($query) {
                $query->where('vendor_id', Auth::user()->vendor->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelledOrders = Order::where('order_status', 'cancelled')
            ->whereHas('orderProducts', function ($query) {
                $query->where('vendor_id', Auth::user()->vendor->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $completedOrders = Order::where('order_status', 'delivered')
            ->where('payment_status', 'completed')
            ->whereHas('orderProducts', function ($query) {
                $query->where('vendor_id', Auth::user()->vendor->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $lowStockProducts = Product::where('vendor_id', Auth::user()->vendor->id)
            ->where('qty', '<=', 10)->get();

        // Merge all collections and maintain descending order
        $notifications = collect()
            ->merge($totalPendingOrder)
            ->merge($cancelledOrders)
            ->merge($completedOrders)
            ->merge($lowStockProducts)
            ->sortByDesc('created_at');

        return view('vendor.notification.index', compact(
            'notifications',
            'totalPendingOrder',
            'cancelledOrders',
            'completedOrders',
            'lowStockProducts'
        ));
    }
}
