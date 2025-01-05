<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Notification index page [frontend]
    public function index()
    {
        $totalPendingOrder = Order::where('order_status', 'pending')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $cancelledOrders = Order::where('order_status', 'cancelled')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $completedOrders = Order::where('order_status', 'delivered')
            ->where('payment_status', 'completed')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Merge all collections and maintain descending order
        $notifications = collect()
            ->merge($totalPendingOrder)
            ->merge($cancelledOrders)
            ->merge($completedOrders)
            ->sortByDesc('created_at');

        return view('frontend.dashboard.notification.index', compact(
            'notifications',
            'totalPendingOrder',
            'cancelledOrders',
            'completedOrders'
        ));
    }
}
