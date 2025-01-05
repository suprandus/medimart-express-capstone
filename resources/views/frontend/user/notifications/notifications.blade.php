@extends('frontend.dashboard.layouts.master')

@section('content')
<div class="container" style="margin-top: 50px;">
    <div id="notification-preview" class="notification-preview">
        <div class="list-group">
            @if(auth()->check())
                @if($notifications == null)
                    <div class="list-group-item list-group-item-action" 
                        style="background-color: #cce5ff; 
                                border: 1px solid #004085;
                                border-radius: 5px;
                                margin: 5px 10px 5px 5px;">
                        <div class="d-flex w-100 justify-content-between">
                            <p class="mb-1">You don't have any Notifications</p>
                        </div>
                    </div>
                @else
                    @if(auth()->user()->role == 'user')
                        @foreach ($notifications as $notification)
                            <div class="list-group-item list-group-item-action" 
                                style="background-color: {{ $notification->status == 'unread' ? '#a7f783' : '#cce5ff' }}; 
                                        border: 1px solid {{ $notification->status == 'unread' ? '#7fcf5b' : '#004085' }}; 
                                        border-radius: 5px; 
                                        margin: 5px 10px 5px 5px;">
                                <div class="d-flex w-100 justify-content-between">
                                    <p class="mb-1"><strong>{{ $notification->type == null ? 'Notification' : $notification->type }}</strong></p>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notification->text }}
                                    <a href="{{ route('user.orders.show', $notification->order_id) }}" 
                                        class="order-link" data-id="{{ $notification->notification_id }}">Order ID: {{$notification->order_id}}
                                    </a>
                                </p>
                                <small>{{ ucfirst($notification->status) }}</small>    
                            </div>
                        @endforeach
                    @elseif(auth()->user()->role == 'vendor')
                        @foreach ($notifications as $notification)
                            <div class="list-group-item list-group-item-action" 
                                style="background-color: {{ $notification->status == 'unread' ? '#a7f783' : '#cce5ff' }}; 
                                        border: 1px solid {{ $notification->status == 'unread' ? '#7fcf5b' : '#004085' }}; 
                                        border-radius: 5px; 
                                        margin: 5px 10px 5px 5px;">
                                <div class="d-flex w-100 justify-content-between">
                                    <p class="mb-1"><strong>{{ $notification->type == null ? 'Notification' : $notification->type }}</strong></p>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notification->text }}
                                    <a href="{{ route('user.orders.show', $notification->order_id) }}" 
                                        class="order-link" data-id="{{ $notification->notification_id }}">Order ID: {{$notification->order_id}}
                                    </a>
                                </p>
                                <small>{{ ucfirst($notification->status) }}</small>    
                            </div>
                        @endforeach
                    @endif
                @endif
            @else
                <div class="list-group-item list-group-item-action" 
                style="background-color: #cce5ff; 
                        border: 1px solid #004085;
                        border-radius: 5px;
                        margin: 5px 2.5px 5px 2.5px;">
                <div class="d-flex w-100 justify-content-between">
                    <p class="mb-1"><strong>You don't have any Notifications</strong></p>
                </div>
            @endif
        </div>
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