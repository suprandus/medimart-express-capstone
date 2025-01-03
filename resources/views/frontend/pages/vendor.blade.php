@extends('frontend.layouts.master')

@section('title')
{{$settings->site_name}} || Pharmacies
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
                    <h4>pharmacies</h4>
                    <ul>
                        <li><a href="{{url('/')}}">home</a></li>
                        <li><a href="javascript:;">pharmacies</a></li>
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
      VENDORS START
    ==============================-->
    <section id="wsus__product_page" class="wsus__vendors">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        @foreach ($vendors as $vendor)
                            <div class="col-xl-6 col-md-6 col-sm-12 mb-4">
                                <div class="card shadow-lg border-0 rounded-lg p-3">
                                    <div class="row align-items-center">
                                        <!-- Circular Image -->
                                        <div class="col-md-4 d-flex justify-content-center align-items-center">
                                            <div class="image-container" style="width: 150px; height: 150px; overflow: hidden; border-radius: 50%; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                                                <img src="{{ asset($vendor->banner) }}" alt="vendor" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        </div>
    
                                        <!-- Vendor Details -->
                                        <div class="col-md-8">
                                            <h4 class="card-title text-primary mb-3">{{ $vendor->shop_name }}</h4>
                                            <p><i class="fas fa-phone-alt"></i> <strong>Phone:</strong> <a href="tel:{{ $vendor->phone }}" class="text-dark">{{ $vendor->phone }}</a></p>
                                            <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <a href="mailto:{{ $vendor->email }}" class="text-dark">{{ $vendor->email }}</a></p>
                                            <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> {{ $vendor->address }}</p>
                                            <a href="{{ route('vendor.products', $vendor->id) }}" class="btn btn-primary w-100 mt-3" style="border-radius: 25px;">Visit Pharmacy</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
    
                <!-- Pagination -->
                <div class="col-xl-12">
                    <section id="pagination">
                        <div class="mt-5">
                            @if ($vendors->hasPages())
                                {{ $vendors->links() }}
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
    
    
<!--============================
       VENDORS END
    ==============================-->
@endsection