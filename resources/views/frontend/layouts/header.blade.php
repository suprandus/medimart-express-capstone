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
                        <li><a href="{{route('user.wishlist.index')}}"><i class="fal fa-heart"></i><span
                                    id="wishlist_count">
                                    @if (auth()->check())
                                    {{\App\Models\Wishlist::where('user_id', auth()->user()->id)->count()}}
                                    @else
                                    0
                                    @endif
                                </span></a></li>
                        {{-- <li><a href="compare.html"><i class="fal fa-random"></i><span>03</span></a></li> --}}
                        <li><a class="wsus__cart_icon" href="#"><i class="fal fa-shopping-bag"></i><span
                                    id="cart-count">{{Cart::content()->count()}}</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="wsus__mini_cart">
        <h4>shopping cart <span class="wsus_close_mini_cart"><i class="far fa-times"></i></span></h4>
        <ul class="mini_cart_wrapper">
            @foreach (Cart::content() as $sidebarProduct)
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
                        {{$settings->currency_icon}}{{$sidebarProduct->price}}
                    </p>
                    <small>Variants total:
                        {{$settings->currency_icon}}{{$sidebarProduct->options->variants_total}}</small>
                    <br>
                    <small>Qty: {{$sidebarProduct->qty}}</small>
                </div>
            </li>
            @endforeach
            @if (Cart::content()->count() === 0)
            <li class="text-center">Cart Is Empty!</li>
            @endif
        </ul>
        <div class="mini_cart_actions {{Cart::content()->count() === 0 ? 'd-none': ''}}">
            <h5>sub total <span id="mini_cart_subtotal">{{$settings->currency_icon}}{{getCartTotal()}}</span></h5>
            <div class="wsus__minicart_btn_area">
                <a class="common_btn" href="{{route('cart-details')}}">view cart</a>
                <a class="common_btn" href="{{route('user.checkout')}}">checkout</a>
            </div>
        </div>
    </div>
</header>
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
                        searchInput.value = data.extractedText.replace(/\n/g, ', '); // Replace newlines with commas for search
                        overlay.style.display = 'none'; // Close overlay
                        document.querySelector('form[action="{{ route('products.index') }}"]').submit(); // Trigger search
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
    .wsus__search {
        display: flex;
        align-items: center;
    }
    .wsus__search form {
        flex-grow: 1;
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
