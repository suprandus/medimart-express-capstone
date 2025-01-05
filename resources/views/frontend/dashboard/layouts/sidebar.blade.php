<div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
                <div class="sidebar-brand">
                        <a href="{{ route('home') }}">{{ $settings->site_name }}</a>
                </div>
                <div class="sidebar-brand sidebar-brand-sm">
                        <a href="">ME</a>
                </div>
                <ul class="sidebar-menu">
                        <li class="menu-header">Dashboard</li>
                        <li class="{{setActive(['user.dashboard'])}}">
                                <a class="nav-link" href="{{ route('user.dashboard') }}"><i
                                                class="fas fa-prescription-bottle-alt"></i><span>Dashboard</span></a>
                        </li>

                        <li class=""><a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home"></i>
                                        <span>Home</span></a>
                        </li>

                        <li class="{{setActive(['user.messages.*'])}}"><a class="nav-link" href="{{
                                        route('user.messages.index') }}"><i class="fas fa-envelope"></i>
                                        <span>Messages</span></a>
                        </li>

                        <li class="{{setActive(['user.orders.*'])}}"><a class="nav-link"
                                        href=" {{ route('user.orders.index' )}}"><i class="fas fa-box"></i>
                                        <span>Orders</span></a>
                        </li>

                        <!-- Notification Section -->
                        <li class="{{setActive(['user.notifications.index'])}}"><a class="nav-link"
                                        href="{{ route('user.notifications.index') }}"><i class="fas fa-bell"></i>
                                        <span>Notifications</span></a>
                        </li>

                        <li class="{{setActive(['user.review.*'])}}"><a class="nav-link"
                                        href="{{route('user.review.index')}}"><i class="fas fa-star"></i>
                                        <span>Reviews</span></a>
                        </li>

                        <li class="{{setActive(['user.address.*'])}}"><a class="nav-link"
                                        href="{{route('user.address.index')}}"><i class="fas fa-map-marker-alt"></i>
                                        <span>Addresses</span></a>
                        </li>

                        <li class="{{setActive(['user.vendor-request.*'])}}"><a class="nav-link"
                                        href="{{route('user.vendor-request.index')}}"><i
                                                class="fas fa-check-circle"></i>
                                        <span>Pharmacy Application</span></a>
                        </li>
                </ul>

        </aside>
</div>