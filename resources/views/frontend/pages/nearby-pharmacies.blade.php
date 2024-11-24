@extends('frontend.layouts.master')

@section('title')
{{ $settings->site_name }} || Nearby Pharmacies
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
                    <h4>Nearby Pharmacies</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">home</a></li>
                        <li><a href="javascript:;">nearby pharmacies</a></li>
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
        NEARBY PHARMACIES PAGE START
    ==============================-->
<section id="wsus__contact">
    <div class="container">
        <div class="wsus__contact_area">
            <div id="nearbypharmacycontainer">
                <form action="{{ route('nearest-vendors') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <!-- Hidden Latitude and Longitude Inputs -->
                        <input type="text" name="latitude" id="latitude" readonly hidden>
                        <input type="text" name="longitude" id="longitude" readonly hidden>
                        <input type="text" name="user_address" id="user_address" readonly hidden>

                        <div class="mb-2 d-flex">
                            <!-- Search Icon and Input Field -->
                            <div class="input-group me-2" style="flex: 1;">
                                <span class="input-group-text"><i class="fal fa-search"></i></span>
                                <input type="text" id="address-input" placeholder="Search for an address" class="form-control">
                            </div>

                            <!-- Submit Button -->
                            <input type="submit" value="Search" class="btn btn-primary">
                        </div>

                        <!-- Google Map -->
                        <div id="map"></div>

                        <!-- Map JavaScript -->
                        <script>
                            function initMap() {
                                const defaultLocation = { lat: 10.3168646, lng: 123.9649095 };

                                // Initialize map, geocoder, and marker
                                map = new google.maps.Map(document.getElementById("map"), {
                                    center: defaultLocation,
                                    zoom: 8,
                                });
                                geocoder = new google.maps.Geocoder();
                                marker = new google.maps.Marker({
                                    position: defaultLocation,
                                    map: map,
                                    draggable: true,
                                    title: "Drag to set location"
                                });

                                // Update latitude, longitude, and address when marker is moved
                                marker.addListener("dragend", function(event) {
                                    const lat = event.latLng.lat();
                                    const lng = event.latLng.lng();
                                    document.getElementById('latitude').value = lat;
                                    document.getElementById('longitude').value = lng;
                                    geocodeLatLng(geocoder, lat, lng);
                                });

                                const input = document.getElementById('address-input');
                                autocomplete = new google.maps.places.Autocomplete(input);
                                autocomplete.bindTo('bounds', map);
                                autocomplete.setFields(['address_components', 'geometry', 'name']);

                                autocomplete.addListener('place_changed', function() {
                                    const place = autocomplete.getPlace();
                                    if (!place.geometry) {
                                        console.error("Place has no geometry");
                                        return;
                                    }

                                    if (place.geometry.viewport) {
                                        map.fitBounds(place.geometry.viewport);
                                    } else {
                                        map.setCenter(place.geometry.location);
                                        map.setZoom(17);
                                    }

                                    marker.setPosition(place.geometry.location);
                                    document.getElementById('latitude').value = place.geometry.location.lat();
                                    document.getElementById('longitude').value = place.geometry.location.lng();
                                    geocodeLatLng(geocoder, place.geometry.location.lat(), place.geometry.location.lng());
                                });
                            }

                            function geocodeLatLng(geocoder, lat, lng) {
                                const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
                                geocoder.geocode({ location: latlng }, function(results, status) {
                                    if (status === 'OK') {
                                        if (results[0]) {
                                            const address = results[0].formatted_address;
                                            document.getElementById('user_address').value = address;
                                        } else {
                                            console.error('No results found');
                                        }
                                    } else {
                                        console.error('Geocoder failed due to: ' + status);
                                    }
                                });
                            }
                        </script>

<script
src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initMap"
async defer>
</script>
                    </div>
                </form>
                @if(isset($vendors) && $vendors->isNotEmpty())
    <div class="mt-4 text-center">
        <h3 class="display-4 text-primary mb-4">Nearby Pharmacies in {{ $selectedLocation }}</h3>
        <div class="container">
            <div class="row">
                <div class="">
        <div class="row">
            @foreach ($vendors as $vendor)
            <div class="col-xl-6 col-md-6 col-sm-12 mb-4">
                <div class="card shadow-lg border-0 rounded-lg">
                    <img src="{{ asset($vendor->banner) }}" alt="vendor" class="card-img-top rounded-top" style="height: 250px; object-fit: cover;">
                    <div class="card-body p-4">
                        <h4 class="card-title text-primary text-center mb-3">{{ $vendor->shop_name }}</h4>
                        <div class="text-left">
                            <p><i class="fas fa-phone-alt"></i> <strong>Phone:</strong> <a href="tel:{{ $vendor->phone }}" class="text-dark">{{ $vendor->phone }}</a></p>
                            <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <a href="mailto:{{ $vendor->email }}" class="text-dark">{{ $vendor->email }}</a></p>
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> {{ $vendor->address }}</p>
                            <p><strong>Distance:</strong> {{ round($vendor->distance, 2) }} km</p>
                        </div>
        
                        <!-- Directions Map -->
                        <div class="mt-3">
                            <div class="map-container" style="padding: 10px; background-color: #f9f9f9; border-radius: 15px; overflow: hidden;">
                                <iframe
                                    width="100%"
                                    height="300"
                                    frameborder="0"
                                    style="border: 0; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);"
                                    src="https://www.google.com/maps/embed/v1/directions?key={{ config('services.google_maps.api_key') }}&origin={{ $latitude }},{{ $longitude }}&destination={{ $vendor->latitude }},{{ $vendor->longitude }}&mode=driving"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        
        
                        <a href="{{ route('vendor.products', $vendor->id) }}" class="btn btn-primary w-100 mt-3" style="border-radius: 25px;">Visit Pharmacy</a>
                    </div>
                </div>
            </div>
        @endforeach
        
        </div>
    </div>
@else
    <p class="mt-4 text-center" style="font-size: 1.2rem; font-weight: bold;">No nearby pharmacies found.</p>
@endif

            
            

            
            
            </div>
        </div>
    </div>
</section>

<!--============================
        NEARBY PHARMACIES PAGE END
    ==============================-->

@endsection
