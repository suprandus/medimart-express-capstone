<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<header>
    <div class="container">
        <div class="row">
            <div class="col-2 col-md-1 d-lg-none">
                <div class="wsus__mobile_menu_area">
                    <span class="wsus__mobile_menu_icon"><i class="fal fa-bars"></i></span>
                </div>
            </div>
            <div class="col-xl-2 col-7 col-md-8 col-lg-2">
                <div class="wsus_logo_area">
                   <a class="wsus__header_logo" href="{{url('/')}}">
                        <img src="{{asset($logoSetting->logo)}}" alt="logo" class="img-fluid w-100">
                    </a>
                </div>
            </div>
            <div class="col-xl-5 col-md-6 col-lg-4 d-none d-lg-block">
                <div class="wsus__search d-flex align-items-center">
                    <form action="{{route('products.index')}}" class="flex-grow-1">
                        <input type="text" placeholder="e.g., Paracetamol, Ibuprofen" name="search" value="{{request()->search}}">
                        <button type="submit"><i class="far fa-search"></i></button>
                    </form>
                    <div class="ms-3">
                        {{-- PRESCRIPTION FILTERING --}}
                        <span class="prescription-icon">
                            <i class="bi bi-prescription"></i>
                        </span>
                    </div>
                </div>
            </div>            
            <div class="col-xl-5 col-3 col-md-3 col-lg-6">
                <div class="wsus__call_icon_area">
                    <div class="wsus__call_area">
                        <div class="wsus__call">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="wsus__call_text">
                            <p style="text-transform: lowercase;">{{ $settings->contact_email }}</p>
                            <p style="text-transform: lowercase;">{{ $settings->contact_phone }}</p>
                        </div>
                    </div>
                    <ul class="wsus__icon_area">
                        <li class="nav-item dropdown">
                            <a href="javascript:void(0);" id="notification-icon" class="nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                @if(auth()->check())
                                    @if(auth()->user()->role == 'user' && $notificationsUserCount != 0)
                                        <span>{{$notificationsUserCount}}</span>
                                    @elseif(auth()->user()->role == 'vendor' && $notificationsPharmacyCount != 0)
                                        <span>{{$notificationsPharmacyCount}}</span>
                                    @endif
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-right notification-preview" aria-labelledby="notification-icon">
                                @if(auth()->check())
                                    @if(auth()->user()->role == 'user')
                                        @if($notificationsUserItems->isEmpty())
                                            <div class="notification-container notification-preview">
                                                <div class="dropdown-item list-group-item list-group-item-action">
                                                    <a class="dropdown-item">No notifications</a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="notification-container notification-preview">
                                                @foreach($notificationsUserItems as $notification)
                                                    <div class="dropdown-item list-group-item list-group-item-action" 
                                                        href="{{ route('user.orders.show', $notification->order_id) }}" 
                                                        data-id="{{ $notification->notification_id }}" 
                                                        style="background-color: {{ $notification->status == 'unread' ? '#a7f783' : '#cce5ff' }}; 
                                                        border: 1px solid {{ $notification->status == 'unread' ? '#7fcf5b' : '#004085' }}; 
                                                        margin: 5px 5px 5px 5px;">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <p class="mb-1"><strong>{{ $notification->type ?? 'Notification' }}</strong></p>
                                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-1">{{ $notification->text }}</p>
                                                        <small>{{ ucfirst($notification->status) }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-center">
                                                <a class="dropdown-item text-center" href="{{ route('user.notifications') }}">View All Notifications</a>
                                                <a class="dropdown-item text-center" href="javascript:void(0);" id="mark-all-read">Mark all as read</a>
                                            </div>
                                        @endif
                                    @elseif(auth()->user()->role == 'vendor')
                                        @if($notificationsPharmacyItems->isEmpty())
                                            <a class="dropdown-item">No notifications</a>
                                        @else
                                            <div class="notification-container notification-preview">
                                                @foreach($notificationsPharmacyItems as $notification)
                                                    <div class="dropdown-item list-group-item list-group-item-action" 
                                                        href="{{ route('user.orders.show', $notification->order_id) }}" 
                                                        data-id="{{ $notification->notification_id }}" 
                                                        style="background-color: {{ $notification->status == 'unread' ? '#a7f783' : '#cce5ff' }}; 
                                                        border: 1px solid {{ $notification->status == 'unread' ? '#7fcf5b' : '#004085' }}; 
                                                        margin: 5px 5px 5px 5px;">
                                                        <div class="d-flex w-100 justify-content-between">
                                                            <p class="mb-1"><strong>{{ $notification->type ?? 'Notification' }}</strong></p>
                                                            <small>{{ $notification->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-1">{{ $notification->text }}</p>
                                                        <small>{{ ucfirst($notification->status) }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-center">
                                                <a class="dropdown-item text-center" href="{{ route('user.notifications') }}">View All Notifications</a>
                                                <a class="dropdown-item text-center" href="javascript:void(0);" id="mark-all-read">Mark all as read</a>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            </div>
                        </li>
                        <li>
                            <a href="{{route('user.wishlist.index')}}">
                                <i class="bi bi-heart"></i>
                                @if (auth()->check())
                                    @php
                                        $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->user()->id)->count();
                                    @endphp
                                    @if($wishlistCount != 0)
                                        <span>{{$wishlistCount}}</span>
                                    @endif
                                @else
                                @endif
                            </a>
                        </li>
                        <li>
                            <a class="wsus__cart_icon" href="#">
                                <i class="bi bi-cart"></i>
                                @if (auth()->check())
                                    @if($cartItemsCount != 0)
                                        <span>{{$cartItemsCount}}</span>
                                    @endif
                                @else
                                    @if(PackageCart::content()->count() != 0)
                                        <span>{{PackageCart::content()->count()}}</span>
                                    @endif
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="wsus__mini_cart">
        <h4>shopping cart <span class="wsus_close_mini_cart"><i class="far fa-times"></i></span></h4>
        <ul class="mini_cart_wrapper">
            @if (auth()->check())
                @foreach (\App\Models\Cart::where('user_id', auth()->user()->id)->get() as $sidebarProduct)
                <li id="mini_cart_{{$sidebarProduct->id}}">
                    <div class="wsus__cart_img">
                        <a href="#"><img src="{{asset($sidebarProduct->product_image)}}" alt="product"
                                class="img-fluid w-100"></a>
                        <a class="wsis__del_icon remove_sidebar_product" data-id="{{$sidebarProduct->id}}" href="#"><i
                                class="fas fa-minus-circle"></i></a>
                    </div>
                    <div class="wsus__cart_text">
                        <a class="wsus__cart_title"
                            href="{{route('product-detail', $sidebarProduct->product_id)}}">{{$sidebarProduct->product_name}}</a>
                        <p>
                            {{$settings->currency_icon}}{{number_format($sidebarProduct->product_price, 2)}}
                        </p>
                        <small>Qty: {{$sidebarProduct->quantity}}</small>
                    </div>
                </li>
                @endforeach
                @if (\App\Models\Cart::where('user_id', auth()->user()->id)->count() == 0)
                    <li class="text-center">Cart Is Empty!</li>
                @endif
            @else
                @foreach (PackageCart::content() as $sidebarProduct)
                <li id="mini_cart_{{$sidebarProduct->rowId}}">
                    <div class="wsus__cart_img">
                        <a href="#"><img src="{{asset($sidebarProduct->options->image)}}" alt="product"
                                class="img-fluid w-100"></a>
                        <a class="wsis__del_icon remove_sidebar_product" data-id="{{$sidebarProduct->rowId}}" href="#"><i
                                class="fas fa-minus-circle"></i></a>
                    </div>
                    <div class="wsus__cart_text">
                        <a class="wsus__cart_title"
                            href="{{route('product-detail', $sidebarProduct->options->slug)}}">{{$sidebarProduct->name}}</a>
                        <p>
                            {{$settings->currency_icon}}{{number_format($sidebarProduct->price, 2)}}
                        </p>
                        <small>Variants total:
                            {{$settings->currency_icon}}{{$sidebarProduct->options->variants_total}}</small>
                        <br>
                        <small>Qty: {{$sidebarProduct->qty}}</small>
                    </div>
                </li>
                @endforeach
                @if (PackageCart::content()->count() === 0)
                <li class="text-center">Cart Is Empty!</li>
                @endif
            @endif
        </ul>
        @if (auth()->check())
            <div class="mini_cart_actions ">
                <h5>sub total <span id="mini_cart_subtotal">{{$settings->currency_icon}}{{getCartTotal()}}</span></h5>
                <div class="wsus__minicart_btn_area">
                    <a class="common_btn" href="{{route('cart-details')}}">view cart</a>
                    {{-- <a class="common_btn" href="{{route('user.checkout')}}">checkout</a> --}}
                </div>
            </div>
        @else
            <div class="mini_cart_actions {{PackageCart::content()->count() === 0 ? 'd-none': ''}}">
                <h5>sub total <span id="mini_cart_subtotal">{{$settings->currency_icon}}{{number_format(getCartTotal(), 2)}}</span></h5>
                <div class="wsus__minicart_btn_area">
                    <a class="common_btn" href="{{route('login')}}">view cart</a>
                    {{-- <a class="common_btn" href="{{route('user.checkout')}}">checkout</a> --}}
                </div>
            </div>
        @endif
    </div>
</header>

{{-- Prescription Upload Overlay --}}
<div id="prescription-upload-overlay" class="overlay">
    <div class="overlay-content">
        <h3>Upload Prescription</h3>
        <form id="prescription-upload-form" enctype="multipart/form-data">
            @csrf
            <label for="prescription-image">Upload your prescription image:</label>
            <input type="file" id="prescription-image" name="image" accept="image/*" required>
            <div id="image-preview-container" style="margin-top: 20px; display: none;">
                <img id="image-preview" src="#" alt="Image Preview" style="max-width: 100%; border-radius: 10px;">
            </div>
            <button type="button" id="submit-prescription" class="btn btn-primary mt-3">Submit</button>
            <button class="btn btn-secondary mt-3 close-overlay">Cancel</button>
        </form>
        <div id="ocr-result"></div>
    </div>
</div>

{{-- <div id="" class="notification-preview">
    <div class="list-group">
        @if(auth()->check())
            @if($notificationsUserItems == null || $notificationsPharmacyItems == null)
                <div class="list-group-item list-group-item-action" 
                    style="background-color: #cce5ff; 
                            border: 1px solid #004085;
                            border-radius: 5px;
                            margin: 5px 10px 5px 5px;">
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1"><strong>You don't have any Notifications</strong></p>
                    </div>
                </div>
            @else
                @if(auth()->user()->role == 'user')
                    @foreach ($notificationsUserItems as $notification)
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
                    @foreach ($notificationsPharmacyItems as $notification)
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
</div> --}}

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.getElementById('prescription-upload-overlay');
        const prescriptionIcon = document.querySelector('.prescription-icon');
        const closeOverlayButton = document.querySelector('.close-overlay');
        const fileInput = document.getElementById('prescription-image');
        const imagePreviewContainer = document.getElementById('image-preview-container');
        const imagePreview = document.getElementById('image-preview');
        const submitButton = document.getElementById('submit-prescription');
        const searchInput = document.querySelector('input[name="search"]');
        const ocrResult = document.getElementById('ocr-result');
        const notificationIcon = document.getElementById('notification-icon');
        const notificationPreview = document.getElementById('notification-preview');
        const markAllRead = document.getElementById('mark-all-read');

        document.querySelectorAll('.dropdown-item').forEach(function (link) {
        link.addEventListener('click', function (event) {
            const notificationId = this.dataset.id;
            if (notificationId) {
                fetch('{{ route('user.view-notification', '') }}/' + notificationId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order_id: this.dataset.orderId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = this.href;
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });

    if (markAllRead) {
        markAllRead.addEventListener('click', function () {
            fetch('{{ route('user.mark-all-read') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
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
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = orderLink;
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => console.error('Fetch Error:', error));
            });
        });
        
        notificationIcon.addEventListener('click', function (event) {
            event.preventDefault();
            notificationPreview.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!notificationIcon.contains(event.target) && !notificationPreview.contains(event.target)) {
                notificationPreview.classList.remove('show');
            }
        });

        prescriptionIcon.addEventListener('click', () => {
            overlay.style.display = 'flex';
        });

        closeOverlayButton.addEventListener('click', (event) => {
            event.preventDefault();
            overlay.style.display = 'none';
            imagePreviewContainer.style.display = 'none';
            imagePreview.src = '';
            fileInput.value = '';
            ocrResult.textContent = '';
        });

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                overlay.style.display = 'none';
                imagePreviewContainer.style.display = 'none';
                imagePreview.src = '';
                fileInput.value = '';
                ocrResult.textContent = '';
            }
        });
        
        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                imagePreviewContainer.style.display = 'none';
                imagePreview.src = '';
            }
        });

        submitButton.addEventListener('click', () => {
            const formData = new FormData(document.getElementById('prescription-upload-form'));
            fetch("{{ route('process.prescription') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.extractedText) {
                        ocrResult.textContent = "Prescription Text: " + data.extractedText;
                        searchInput.value = data.extractedText.replace(/\n/g, ', ');
                        overlay.style.display = 'none';
                        document.querySelector('form[action="{{ route('products.index') }}"]').submit();
                    } else {
                        ocrResult.textContent = "Failed to process image.";
                    }
                })
                .catch(error => {
                    console.error("Error processing prescription:", error);
                    ocrResult.textContent = "An error occurred.";
                });
        });
    });
</script>
<style>
    .notification-preview {
        max-height: 300px;
        max-width: 400px;
        overflow-y: auto;
        overflow-x: hidden; /* Remove horizontal scrollbar */
    }

    .notification-preview::-webkit-scrollbar {
        width: 8px;
    }

    .notification-preview::-webkit-scrollbar-thumb {
        background-color: blue;
        border-radius: 10px;
    }

    .notification-preview .list-group-item {
        padding: 10px;
    }

    .notification-preview.show {
        display: block;
    }

    .notification-container {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden; /* Remove horizontal scrollbar */
    }
    .dropdown-item{
        max-width: 380px;
    }

    .notification-container .list-group-item {
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
    }

    .d-flex.justify-content-center {
        justify-content: center;
        gap: 10px; /* Add space between the links */
    }
    .prescription-icon {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 1.2rem;
        cursor: pointer;
    }
    .prescription-icon i {
        color: white;
        font-size: 30px;
    }
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .overlay-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 90%;
        max-width: 400px;
    }

    .overlay-content h3 {
        margin-bottom: 20px;
    }

    .overlay-content button {
        display: inline-block;
        width: 100%;
    }
    #image-preview {
        max-height: 300px;
        border: 1px solid #ccc;
        padding: 5px;
    }

    #image-preview-container {
        text-align: center;
    }
</style>
