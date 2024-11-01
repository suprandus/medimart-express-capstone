@extends('frontend.dashboard.layouts.master')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>User Dashboard</h1>
  </div>
  <div class="row">
    <!-- Wishlist Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('user.wishlist.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-medkit"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Wishlist Supplies</h4>
            </div>
            <div class="card-body">
              {{ $wishlist }}
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Total Orders Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{ route('user.orders.index' )}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-cart-plus"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Orders</h4>
            </div>
            <div class="card-body">
              {{ $totalOrder }}
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Product Reviews Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('user.review.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-star"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Reviews</h4>
            </div>
            <div class="card-body">
              {{ $reviews }}
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Completed Orders Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('product-traking.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Track Orders</h4>
            </div>
            <div class="card-body">
              <i class="fas fa-arrow-right"></i>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
</section>
@endsection