@extends('frontend.dashboard.layouts.master')

@section('content')
<div class="container" style="margin-top: 50px;">
    <div class="list-group">
        @foreach ($notifications as $notification)
            <div class="list-group-item list-group-item-action" 
                 style="background-color: {{ $notification->status == 'unread' ? '#a7f783' : '#cce5ff' }}; 
                        border: 1px solid {{ $notification->status == 'unread' ? '#7fcf5b' : '#004085' }};">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $notification->type }}</h5>
                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-1">{{ $notification->text }}
                    @if(auth()->user()->role == 'vendor')
                        <a href="{{ route('vendor.orders.show', $notification->order_id) }}" 
                            class="order-link" data-id="{{ $notification->notification_id }}">Order ID: {{$notification->order_id}}
                        </a>
                    @elseif(auth()->user()->role == 'user')
                        <a href="{{ route('user.orders.show', $notification->order_id) }}" 
                            class="order-link" data-id="{{ $notification->notification_id }}">Order ID: {{$notification->order_id}}
                        </a>
                    @endif
                </p>
                <small>Status: {{ ucfirst($notification->status) }}</small>    
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.order-link').forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                const notificationId = this.dataset.id;
                const orderLink = this.href;
                fetch('{{ route('user.view-notification', '') }}/' + notificationId, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = orderLink;
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
@endpush