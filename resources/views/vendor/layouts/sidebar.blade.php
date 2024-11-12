<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="{{ route('home') }}">{{ $settings->site_name }}</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="">||</a>
    </div>
    <ul class="sidebar-menu">
      <li class="menu-header">Dashboard</li>
      <li class="{{setActive(['vendor.dashbaord'])}}">
        <a class="nav-link" href="{{ route('vendor.dashbaord') }}"><i class="fas fa-prescription-bottle-alt"></i><span>
            Dashboard</span></a>
      </li>
      <li class=""><a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home"></i>
          <span> Home</span></a>
      </li>
      <li class="{{setActive(['vendor.messages.*'])}}"><a class="nav-link"
          href="{{ route('vendor.messages.index') }}"><i class="fas fa-envelope"></i>
          <span> Messages</span></a>
      </li>
      <li class="{{setActive(['vendor.orders.*'])}}"><a class="nav-link" href=" {{ route('vendor.orders.index' )}}"><i
            class="fas fa-box"></i>
          <span> Orders</span></a>
      </li>
      <li class="{{setActive(['vendor.products.*'])}}"><a class="nav-link" href="{{route('vendor.products.index')}}"><i
            class="fas fa-shopping-cart"></i>
          <span> Products</span></a>
      </li>
      <li class="{{setActive(['vendor.reviews.*'])}}"><a class="nav-link" href="{{route('vendor.reviews.index')}}"><i
            class="fas fa-star"></i>
          <span> Reviews</span></a>
      </li>
      <li class="{{setActive(['vendor.withdraw.*'])}}"><a class="nav-link" href="{{route('vendor.withdraw.index')}}"><i
            class="fas fa-money-bill-wave"></i>
          <span> Withdraw</span></a>
      </li>

      <li class="{{setActive(['vendor.shop-profile.*'])}}"><a class="nav-link"
          href="{{route('vendor.shop-profile.index')}}"><i class="fas fa-check-circle"></i>
          <span>Pharmacy Profile</span></a>
      </li>
    </ul>
  </aside>
</div>