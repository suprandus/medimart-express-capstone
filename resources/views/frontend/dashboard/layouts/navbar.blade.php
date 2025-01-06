@php

use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

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

// Merge all collections and sort by created_at descending
$notifications = collect()
->merge($totalPendingOrder)
->merge($cancelledOrders)
->merge($completedOrders)
->sortByDesc('created_at');
@endphp

<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
  </form>
  <ul class="navbar-nav navbar-right">
    <!-- Notification dropdown -->
    {{-- <li class="dropdown dropdown-list-toggle">
      <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg beep">
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
          <a href="{{ route('user.orders.show', $notification->id) }}" class="dropdown-item dropdown-item-unread">
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
          <a href="{{ route('user.orders.show', $notification->id) }}" class="dropdown-item dropdown-item-unread">
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
          <a href="{{ route('user.orders.show', $notification->id) }}" class="dropdown-item dropdown-item-unread">
            <div class="dropdown-item-icon bg-success text-white">
              <i class="fas fa-check"></i>
            </div>
            <div class="dropdown-item-desc">
              Order #{{ $notification->invocie_id }} has been completed.
              <div class="time text-success">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
          </a>
          @endif
          @endif
          @endforeach
        </div>
        <div class="dropdown-footer text-center">
          <a href="{{ route('user.notifications') }}">View All <i class="fas fa-chevron-right"></i></a>
        </div>
      </div>
    </li> --}}
    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <img alt="image" style="width: 40px;height: 40px; object-fit: cover;" src="{{asset(auth()->user()->image)}}"
          class="rounded-circle mr-1">
        <div class="d-sm-none d-lg-inline-block">{{auth()->user()->name}}</div>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <a href="{{route('user.vendor-request.index')}}" class="dropdown-item has-icon">
          <i class="far fa-user"></i> Application
        </a>

        <a href="{{route('user.profile')}}" class="dropdown-item has-icon">
          <i class="fas fa-cog"></i> Settings
        </a>
        <div class="dropdown-divider"></div>

        <!-- Authentication -->
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <a href="{{route('logout')}}" onclick="event.preventDefault();
                this.closest('form').submit();" class="dropdown-item has-icon text-danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </form>
      </div>
    </li>
  </ul>
</nav>