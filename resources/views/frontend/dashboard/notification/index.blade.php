@extends('frontend.dashboard.layouts.master')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Notifications</h1>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="activities">

        @foreach ($notifications as $notification)
        @if($notification instanceof \App\Models\Order)
        @if($notification->order_status == 'pending')
        @foreach($notification->orderProducts as $product)
        <div class="activity">
          <div class="activity-icon bg-primary text-white shadow-primary">
            <i class="fas fa-box"></i>
          </div>
          <div class="activity-detail">
            <div class="mb-2">
              <span class="text-job">{{ $notification->created_at->diffForHumans() }}</span>
            </div>
            <p><a href="{{ route('user.orders.show', $notification->id) }}">{{ $product->product_name }}</a> has been
              ordered.
            </p>
          </div>
        </div>
        @endforeach

        @elseif($notification->order_status == 'cancelled')
        @foreach($notification->orderProducts as $product)
        <div class="activity">
          <div class="activity-icon bg-danger text-white shadow-danger">
            <i class="fas fa-times"></i>
          </div>
          <div class="activity-detail">
            <div class="mb-2">
              <span class="text-job">{{ $notification->created_at->diffForHumans() }}</span>
            </div>
            <p>Order <a href="{{ route('user.orders.show', $notification->id) }}">#{{ $notification->invocie_id }}</a>
              has been cancelled.
            </p>
          </div>
        </div>
        @endforeach

        @elseif($notification->order_status == 'delivered' && $notification->payment_status == 'completed')
        <div class="activity">
          <div class="activity-icon bg-success text-white shadow-success">
            <i class="fas fa-check"></i>
          </div>
          <div class="activity-detail">
            <div class="mb-2">
              <span class="text-job">{{ $notification->created_at->diffForHumans() }}</span>
            </div>
            <p>Order <a href="{{ route('user.orders.show', $notification->id) }}">#{{ $notification->invocie_id }}</a>
              has been completed.
            </p>
          </div>
        </div>
        @endif
        @endif
        @endforeach

      </div>
    </div>
  </div>
</section>
@endsection