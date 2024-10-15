@extends('vendor.layouts.master')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Pharmacy Dashboard</h1>
  </div>
  <div class="row">
    <!-- Today's Order Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-cart-plus"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Today's Order</h4>
            </div>
            <div class="card-body">
              {{$todaysOrder}}
            </div>
          </div>
        </div>
      </a>
    </div>

    <!-- Pending Orders Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-hourglass-half"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Pending Orders</h4>
            </div>
            <div class="card-body">
              {{$totalPendingOrder}}
            </div>
          </div>
        </div>
      </a>
    </div>


    <!-- Total Orders Card -->
    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Completed Orders</h4>
            </div>
            <div class="card-body">
              {{ $totalOrder }}
            </div>
          </div>
        </div>
      </a>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('vendor.reviews.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-star"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Reviews</h4>
            </div>
            <div class="card-body">
              {{$totalReviews}}
            </div>
          </div>
        </div>
      </a>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Today's Earnings</h4>
            </div>
            <div class="card-body">
              {{$settings->currency_icon}}{{$todaysOrder}}
            </div>
          </div>
        </div>
      </a>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-chart-bar"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>This Month's Earnings</h4>
            </div>
            <div class="card-body">
              {{$settings->currency_icon}}{{$monthEarnings}}
            </div>
          </div>
        </div>
      </a>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Earnings</h4>
            </div>
            <div class="card-body">
              {{$settings->currency_icon}}{{$toalEarnings}}
            </div>
          </div>
        </div>
      </a>
    </div>

  </div>
</section>
@endsection