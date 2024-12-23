@php
    $categoryProductSliderSectionTwo = json_decode($categoryProductSliderSectionTwo->value);
    $lastKey = [];

    foreach($categoryProductSliderSectionTwo as $key => $category){
        if($category === null ){
            break;
        }
        $lastKey = [$key => $category];
    }

    if(array_keys($lastKey)[0] === 'category'){
        $category = \App\Models\Category::find($lastKey['category']);
        $products = \App\Models\Product::withAvg('reviews', 'rating')->withCount('reviews')
        ->with(['variants', 'category', 'productImageGalleries'])
        ->where('category_id', $category->id)->orderBy('id', 'DESC')->take(12)->get();
    }elseif(array_keys($lastKey)[0] === 'sub_category'){
        $category = \App\Models\SubCategory::find($lastKey['sub_category']);
        $products = \App\Models\Product::withAvg('reviews', 'rating')->withCount('reviews')
        ->with(['variants', 'category', 'productImageGalleries'])
        ->where('sub_category_id', $category->id)->orderBy('id', 'DESC')->take(12)->get();

    }else {
        $category = \App\Models\ChildCategory::find($lastKey['child_category']);
        $products = \App\Models\Product::withAvg('reviews', 'rating')->withCount('reviews')
        ->with(['variants', 'category', 'productImageGalleries'])
        ->where('child_category_id', $category->id)->orderBy('id', 'DESC')->take(12)->get();
    }
@endphp
<section id="wsus__electronic">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="wsus__section_header">
                    <h3>{{$category->name}}</h3>
                    <a class="see_btn" href="{{route('products.index', ['category' => $category->slug])}}">see more <i class="fas fa-caret-right"></i></a>
                </div>
            </div>
        </div>
        <div class="row flash_sell_slider">
            @foreach ($products as $product)
                {{-- Render the x-product-card component and ensure price formatting --}}
                <div class="wsus__product_item">
                    <span class="wsus__new">{{ productType($product->product_type) }}</span>
                    @if (checkDiscount($product))
                        <span class="wsus__minus">-{{ calculateDiscountPercent($product->price, $product->offer_price) }}%</span>
                    @endif
                    <a class="wsus__pro_link" href="{{ route('product-detail', $product->slug) }}">
                        <img src="{{ asset($product->thumb_image) }}" alt="product" class="img-fluid w-100 img_1" />
                        <img src="
                        @if (isset($product->productImageGalleries[0]->image))
                            {{ asset($product->productImageGalleries[0]->image) }}
                        @else
                            {{ asset($product->thumb_image) }}
                        @endif
                        " alt="product" class="img-fluid w-100 img_2" />
                    </a>
                    <div class="wsus__product_details">
                        <a class="wsus__category" href="#">{{ $product->category->name }}</a>
                        <p class="wsus__pro_rating">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $product->reviews_avg_rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                            <span>({{ $product->reviews_count }} reviews)</span>
                        </p>
                        <a class="wsus__pro_name" href="{{ route('product-detail', $product->slug) }}">{{ limitText($product->name, 52) }}</a>
                        <p class="wsus__price">
                            @if (checkDiscount($product))
                                {{$settings->currency_icon}}{{ number_format($product->offer_price, 2) }}
                                <del>{{$settings->currency_icon}}{{ number_format($product->price, 2) }}</del>
                            @else
                                {{$settings->currency_icon}}{{ number_format($product->price, 2) }}
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

