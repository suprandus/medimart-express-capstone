@php

use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

$totalPendingOrder = Order::where('order_status', 'pending')
->whereHas('orderProducts', function($query) {
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
->where('qty', '<=', 10) ->get();

  // Merge all collections and sort by created_at descending
  $notifications = collect()
  ->merge($totalPendingOrder)
  ->merge($cancelledOrders)
  ->merge($lowStockProducts)
  ->merge($completedOrders)
  ->sortByDesc('created_at');

  @endphp

  <nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
      <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </form>
    <ul class="navbar-nav navbar-right">
      <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
          class="nav-link notification-toggle nav-link-lg beep">
          <i class="far fa-bell"></i>
        </a>
        <div class="dropdown-menu dropdown-list dropdown-menu-right">
          <div class="dropdown-header">Notifications</div>
          <div class="dropdown-list-content dropdown-list-icons">
            @foreach($notifications as $notification)

            <!-- Pending order notifications -->
            @if($notification instanceof \App\Models\Order)
            @if($notification->order_status == 'pending')
            @foreach($notification->orderProducts as $product)
            <a href="{{ route('vendor.orders.show', $notification->id) }}" class="dropdown-item dropdown-item-unread">
              <div class="dropdown-item-icon bg-primary text-white">
                <i class="fas fa-box"></i>
              </div>
              <div class="dropdown-item-desc">
                {{ $product->product_name }} has been ordered.
                <div class="time text-primary">{{ $notification->created_at->diffForHumans() }}</div>
              </div>
            </a>
            @endforeach

            <!-- Cancelled order notifications -->
            @elseif($notification->order_status == 'cancelled')
            <a href="{{ route('vendor.orders.show', $notification->id) }}" class="dropdown-item dropdown-item-unread">
              <div class="dropdown-item-icon bg-danger text-white">
                <i class="fas fa-times"></i>
              </div>
              <div class="dropdown-item-desc">
                Order #{{ $notification->invocie_id }} has been cancelled.
                <div class="time text-danger">{{ $notification->created_at->diffForHumans() }}</div>
              </div>
            </a>

            <!-- Completed order notifications -->
            @elseif($notification->order_status == 'delivered' && $notification->payment_status == 'completed')
            <a href="{{ route('vendor.orders.show', $notification->id) }}" class="dropdown-item dropdown-item-unread">
              <div class="dropdown-item-icon bg-success text-white">
                <i class="fas fa-check"></i>
              </div>
              <div class="dropdown-item-desc">
                Order #{{ $notification->invocie_id }} has been completed.
                <div class="time text-success">{{ $notification->created_at->diffForHumans() }}</div>
              </div>
            </a>
            @endif

            <!-- Low on stock notifications -->
            @elseif($notification instanceof \App\Models\Product)
            <a href="{{ route('vendor.products.edit', $notification->id) }}" class="dropdown-item dropdown-item-unread">
              <div class="dropdown-item-icon bg-warning text-white">
                <i class="fas fa-exclamation-triangle"></i>
              </div>
              <div class="dropdown-item-desc">
                {{ $notification->name }} is low on stock.
                <div class="time text-warning">{{ $notification->created_at->diffForHumans() }}</div>
              </div>
            </a>
            @endif
            @endforeach
          </div>
          <div class="dropdown-footer text-center">
            <a href="#">View All <i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </li>

      <li class="dropdown"><a href="#" data-toggle="dropdown"
          class="nav-link dropdown-toggle nav-link-lg nav-link-user">
          <img alt="image" style="width: 40px;height: 40px;
    object-fit: cover;" src="{{asset(auth()->user()->image)}}" class="rounded-circle mr-1">
          <div class="d-sm-none d-lg-inline-block">{{auth()->user()->name}}</div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="{{route('vendor.shop-profile.index')}}" class="dropdown-item has-icon">
            <i class="far fa-user"></i> Profile
          </a>

          <a href="{{ route('vendor.profile') }}" class="dropdown-item has-icon">
            <i class="fas fa-cog"></i> Settings
          </a>
          <div class="dropdown-divider"></div>

          <!-- Authentication -->
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" onclick="event.preventDefault();
        this.closest('form').submit();" class="dropdown-item has-icon text-danger">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </form>
        </div>
      </li>
    </ul>
  </nav>