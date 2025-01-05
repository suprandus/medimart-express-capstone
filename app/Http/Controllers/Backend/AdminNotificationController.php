<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    // Notification index page [admin]
    public function index()
    {
        $totalPendingOrder = Order::where('order_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelledOrders = Order::where('order_status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();

        $completedOrders = Order::where('order_status', 'delivered')
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $lowStockProducts = Product::where('qty', '<=', 10)->orderBy('created_at', 'desc')
            ->get();

        // Merge all collections and sort by created_at descending
        $notifications = collect()
            ->merge($totalPendingOrder)
            ->merge($cancelledOrders)
            ->merge($lowStockProducts)
            ->merge($completedOrders)
            ->sortByDesc('created_at');

        return view('admin.notification.index', compact('notifications'));
    }
}
