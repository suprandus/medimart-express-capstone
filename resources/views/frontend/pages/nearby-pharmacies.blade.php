@extends('frontend.layouts.master')

@section('title')
{{$settings->site_name}} || Nearby Pharmacies
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
                        <li><a href="{{route('home')}}">home</a></li>
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
                        <input type="text" name="longitude" id="longitude" readonly hidden><br><br>
                        <input type="text" name="user_address" id="user_address" readonly hidden>

                        <div class="mb-2 d-flex">
                            <!-- Search Icon and Input Field -->
                            <div class="input-group me-2" style="flex: 1;">
                                <span class="input-group-text"><i class="fal fa-search"></i></span>
                                <input type="text" id="address-input" placeholder="Search for an address"
                                    class="form-control">
                            </div>

                            <!-- Submit Button -->
                            {{-- <input type="submit" value="Search" class="btn btn-primary"> --}}
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
                                            document.getElementById('user_address').value = address; // Set the address in the hidden field
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
                            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBFfWKWjTQueC1E9sqRwJ9E1igYRM5zNYE&libraries=places&callback=initMap"
                            async defer>
                        </script>
                    </div>
                </form>

                @if(isset($vendors) && $vendors->isNotEmpty())
                <div class="mt-4 text-center">
                    <h3>Nearby Pharmacies in {{ $selectedLocation }}</h3>
                    <div class="row">
                        @foreach ($vendors as $vendor)
                        <div class="col-xl-6 col-md-6">
                            <!-- Each vendor takes half the width -->
                            <div class="wsus__vendor_single">
                                <img src="{{ asset($vendor->banner) }}" alt="vendor" class="img-fluid w-100">
                                <div class="wsus__vendor_text">
                                    <div class="wsus__vendor_text_center text-start">
                                        <h4>{{ $vendor->shop_name }}</h4>
                                        <a href="javascript:;"><i class="far fa-phone-alt"></i> {{ $vendor->phone
                                            }}</a>
                                        <a href="javascript:;"><i class="fal fa-envelope"></i> {{ $vendor->email
                                            }}</a>
                                        <a href="javascript:;"><i class="fal fa-map-marker-alt"></i> {{
                                            $vendor->address
                                            }}</a>
                                        <a href="{{ route('vendor.products', $vendor->id) }}"
                                            class="common_btn">Visit
                                            Store</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="mt-4" style="text-align: center; font-size: 1.2rem; font-weight: bold;">No nearby
                    pharmacies
                    found.
                </p>
                @endif
            </div>
        </div>
    </div>
</section>

<!--============================
        NEARBY PHARMACIES PAGE END
    ==============================-->

@endsection