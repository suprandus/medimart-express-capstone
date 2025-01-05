<div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
                <div class="sidebar-brand">
                        <a href="{{ route('home') }}">{{ $settings->site_name }}</a>
                </div>
                <div class="sidebar-brand sidebar-brand-sm">
                        <a href="">ME</a>
                </div>
                <ul class="sidebar-menu">
                        <li class="{{setActive(['user.dashboard'])}}">
                                <a class="nav-link" href="{{ route('user.dashboard') }}"><i
                                                class="fas fa-prescription-bottle-alt"></i><span>Dashboard</span></a>
                        </li>
                        <li class=""><a class="nav-link" href="{{ route('home') }}"><i class="fas fa-home"></i>
                                <span>Home</span></a>
                        </li>
                        <li class="{{setActive(['user.notifications'])}}">
                                <a class="nav-link" href="{{route('user.notifications') }}">
                                    <i class="fas fa-bell"></i>
                                    @php
                                        $notifCount = \App\Models\NotificationsUser::where('user_id', Auth::id())->where('status', 'unread')->count();
                                    @endphp
                                    <span class="mb-1">Notifications</span>
                                    @if($notifCount != 0)
                                        <small class="text-danger font-weight-bold" style="display: inline-block"> {{$notifCount}}</small>
                                    @endif
                                </a>
                        </li>
                        <li class="{{setActive(['user.messages.*'])}}"><a class="nav-link" href="{{
                                        route('user.messages.index') }}"><i class="fas fa-envelope"></i>
                                        <span>Messages</span></a>
                        </li>
                        <li class="{{setActive(['user.orders.*'])}}"><a class="nav-link"
                                        href=" {{ route('user.orders.index' )}}"><i class="fas fa-cart-plus"></i>
                                        <span>Orders</span></a>
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