@php
use App\Models\Order;
use Carbon\Carbon;

$todayOrders = Order::whereDate('created_at', Carbon::today())->get();
@endphp

<nav class="navbar navbar-expand-lg main-navbar">
  <form class="form-inline mr-auto">
    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
  </form>
  <ul class="navbar-nav navbar-right">

    <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown"
        class="nav-link notification-toggle nav-link-lg beep">
        @if($todayOrders->count() > 0)
        <i class="far fa-bell"></i>
        <span class="badge badge-danger navbar-badge">{{ $todayOrders->count() }}</span>
        @endif</a>
      <div class="dropdown-menu dropdown-list dropdown-menu-right">
        <div class="dropdown-header">Notifications
          <div class="float-right">
            {{-- <a href="#">Mark All As Read</a> --}}
          </div>
        </div>
        <div class="dropdown-list-content dropdown-list-icons">
          @foreach($todayOrders as $order)
          @foreach($order->orderProducts as $product)
          <a href="{{ route('vendor.orders.index') }}" class="dropdown-item dropdown-item-unread">
            <div class="dropdown-item-icon bg-primary text-white">
              <i class="fas fa-box"></i>
            </div>
            <div class="dropdown-item-desc">
              {{ $product->product_name }} has been ordered!
              <div class="time text-primary">{{ $order->created_at->diffForHumans() }}</div>
            </div>
          </a>
          @endforeach
          @endforeach
        </div>
        <div class="dropdown-footer text-center">
          <a href="{{ route('vendor.orders.index') }}">View All <i class="fas fa-chevron-right"></i></a>
        </div>
      </div>
    </li>

    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
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