@extends('frontend.layouts.master')

@section('title')
{{$settings->site_name}} || Cart Details
@endsection

@section('content')
<!--============================
        BREADCRUMB START
    ==============================-->
<section id="wsus__breadcrumb">
    <div class="wsus_breadcrumb_overlay">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h4>cart View</h4>
                    <ul>
                        <li><a href="#">home</a></li>
                        <li><a href="#">product</a></li>
                        <li><a href="#">cart view</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!--============================
        BREADCRUMB END
    ==============================-->


<!--============================
        CART VIEW PAGE START
    ==============================-->
<section id="wsus__cart_view">
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                <div class="wsus__cart_list">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr class="d-flex">
                                    <th class="wsus__pro_img">Product Image</th>
                                    <th class="wsus__pro_name">Product Name</th>
                                    <th class="wsus__pro_tk">Price</th>
                                    <th class="wsus__pro_tk">Subtotal</th>
                                    <th class="wsus__pro_select">Quantity</th>
                                    <th class="wsus__pro_icon">
                                        <a href="#" class="common_btn clear_cart">Selet All</a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cartItems as $item)
                                <tr class="d-flex">
                                    <td class="wsus__pro_img">
                                        @if(isset($item->options))
                                            <img src="{{asset($item->options->image_product)}}" alt="product" class="img-fluid w-100">
                                        @else
                                            <img src="{{asset($item->image_product)}}" alt="product" class="img-fluid w-100">
                                        @endif
                                    </td>
                                    <td class="wsus__pro_name">
                                        <p>{!! $item->name ?? $item->product_name !!}</p>
                                        @if(isset($item->options))
                                            @foreach ($item->options->variants as $key => $variant)
                                                <span>{{$key}}: {{$variant['name']}} ({{$settings->currency_icon.$variant['price']}})</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="wsus__pro_tk">
                                        <h6>{{$settings->currency_icon.number_format(($item->product_price), 2)}}</h6>
                                    </td>
                                    <td class="wsus__pro_tk">
                                        <h6 id="total-{{$item->product_id ?? $item->id}}">
                                            {{$settings->currency_icon.number_format(($item->product_price * $item->cart_product_count), 2)}}
                                        </h6>
                                    </td>
                                    <td class="wsus__pro_select">
                                        <div class="product_qty_wrapper">
                                            <button class="btn btn-danger product-decrement"
                                                data-stock="{{$item->product_stock}}"
                                                data-price="{{$item->product_price}}">-
                                            </button>
                                            <input class="product-qty" style="text-align:center;"
                                                data-productid="{{$item->product_id ?? $item->id}}"
                                                type="text" min="1" max="{{$item->product_stock}}"
                                                value="{{$item->cart_product_count}}" readonly />
                                            <button class="btn btn-success product-increment"
                                                data-stock="{{$item->product_stock}}"
                                                data-price="{{$item->product_price}}">+
                                            </button>
                                        </div>
                                    </td>
                                    <td class="wsus__pro_icon">
                                        <input type="checkbox" class="select-product" data-productid="{{$item->product_id ?? $item->id}}" {{ $item->checked == 'yes' ? 'checked' : '' }}>
                                    </td>
                                </tr>
                                @endforeach

                                @if ($cartItems->isEmpty())
                                <tr class="d-flex">
                                    <td class="wsus__pro_icon" rowspan="2" style="width:100%">
                                        Cart is empty!
                                    </td>
                                </tr>
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="wsus__cart_list_footer_button" id="sticky_sidebar">
                    <h6>total cart</h6>
                    <p>subtotal: <span id="sub_total">{{$settings->currency_icon}}{{getCartTotal()}}</span></p>
                    <p>coupon(-): <span id="discount">{{$settings->currency_icon}}{{getCartDiscount()}}</span></p>
                    <p class="total"><span>total:</span> <span id="cart_total">{{$settings->currency_icon}}{{getMainCartTotal()}}</span></p>

                    <form id="coupon_form">
                        <input type="text" placeholder="Coupon Code" name="coupon_code" value="{{session()->has('coupon') ? session()->get('coupon')['coupon_code'] : ''}}">
                        <button type="submit" class="common_btn">apply</button>
                    </form>
                    <a class="common_btn mt-4 w-100 text-center" href="{{route('user.checkout')}}">checkout</a>
                    <a class="common_btn mt-1 w-100 text-center" href="{{route('home')}}"><i class="fab fa-shopify"></i> Keep Shopping</a>
                </div>
            </div>
        </div>
    </div>
</section>
<section id="wsus__single_banner">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="wsus__single_banner_content">
                    @if ($cartpage_banner_section->banner_one->status == 1)
                    <a href="{{$cartpage_banner_section->banner_one->banner_url}}">
                        <img class="img-gluid" src="{{asset($cartpage_banner_section->banner_one->banner_image)}}" alt="">
                    </a>
                    @endif
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="wsus__single_banner_content single_banner_2">
                    @if ($cartpage_banner_section->banner_two->status == 1)
                    <a href="{{$cartpage_banner_section->banner_two->banner_url}}">
                        <img class="img-gluid" src="{{asset($cartpage_banner_section->banner_two->banner_image)}}" alt="">
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.product-increment').on('click', function(){
            let input = $(this).siblings('.product-qty');
            let quantity = parseInt(input.val()) + 1;
            let stock = $(this).data('stock');
            let productId = input.data('productid');
            let price = parseFloat($(this).data('price'));

            if (quantity > stock) {
                toastr.warning('Quantity exceeds available stock.');
                return;
            }

            input.val(quantity);

            $.ajax({
                url: "{{route('cart.update-quantity')}}",
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    // Update the quantity column
                    let totalElement = $('#total-' + productId);
                    totalElement.text('{{$settings->currency_icon}}' + (quantity * price).toFixed(2));
                    toastr.success('Product quantity updated successfully!');
                },
                error: function(response) {
                    toastr.error('Something went wrong!');
                }
            });
        });

        // decrement product quantity
        $('.product-decrement').on('click', function(){
            let input = $(this).siblings('.product-qty');
            let quantity = parseInt(input.val()) - 1;
            let productId = input.data('productid');
            let price = parseFloat($(this).data('price'));

            if (quantity < 1) {
                toastr.warning('Quantity is less than 1. Do you want to remove this item from the cart?');
                if (confirm('Do you want to remove this item from the cart?')) {
                    if (productId) {
                        window.location.href = "{{url('cart/remove-product')}}" + '/' + productId;
                    } else {
                        toastr.error('Unable to remove item, missing item.');
                    }
                } else {
                    input.val(1);
                }
                return;
            }

            input.val(quantity);

            $.ajax({
                url: "{{route('cart.update-quantity')}}",
                method: 'POST',
                data: {
                    product_id: productId,
                    quantity: quantity
                },
                success: function(response) {
                    let totalElement = $('#total-' + productId);
                    totalElement.text('{{$settings->currency_icon}}' + (quantity * price).toFixed(2));
                    toastr.success('Product quantity updated successfully!');
                },
                error: function(response) {
                    toastr.error('Something went wrong!');
                }
            });
        });

        document.querySelectorAll('.select-product').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const productId = this.dataset.productid;
                const checked = this.checked ? 'yes' : 'no';

                fetch('{{ route('cart.update-checked-status') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        checked: checked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log(data.message);
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