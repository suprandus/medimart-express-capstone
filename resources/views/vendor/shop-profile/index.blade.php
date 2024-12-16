@extends('vendor.layouts.master')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Pharmacy Profile</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <form action="{{route('vendor.shop-profile.store')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label>Preview</label>
            <br>
            <img width="200px" src="{{asset($profile->banner)}}" alt="">
          </div>
          <div class="form-group">
            <label>Banner</label>
            <input type="file" class="form-control" name="banner">
          </div>
          <div class="form-group">
            <label>Shop Name</label>
            <input type="text" class="form-control" name="shop_name" value="{{$profile->shop_name}}">
          </div>

          <div class="form-group">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone" value="{{$profile->phone}}">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control" name="email" value="{{$profile->email}}">
          </div>
          <div class="form-group">
            <label>Address</label>
            <input type="text" class="form-control" name="address" id="address" value="{{$profile->address}}" required
              readonly>
            <input type="text" name="latitude" id="latitude" value="{{$profile->latitude}}" readonly hidden>
            <input type="text" name="longitude" id="longitude" value="{{$profile->longitude}}" readonly hidden>
            <br>
            <label>Please pin location on the map</label>
            <input type="text" id="address-input" placeholder="Search for an address" class="form-control mb-2">
            <div id="map" style="height: 400px; width: 100%;"></div>

            <!-- Script to initialize the Google Map, Geocode, and Places Search -->
            <script>
              let map, marker, geocoder, autocomplete;

                    function initMap() {
                        // Default location
                        const defaultLocation = { lat: 10.3168646, lng: 123.9649095 };

                        // Initialize the map
                        map = new google.maps.Map(document.getElementById("map"), {
                            center: defaultLocation,
                            zoom: 8,
                        });

                        // Initialize the geocoder
                        geocoder = new google.maps.Geocoder();

                        // Create a draggable marker
                        marker = new google.maps.Marker({
                            position: defaultLocation,
                            map: map,
                            draggable: true,
                            title: "Drag to set location"
                        });

                        // Update inputs on marker drag end
                        marker.addListener("dragend", function(event) {
                            const lat = event.latLng.lat();
                            const lng = event.latLng.lng();
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;
                            geocodeLatLng(geocoder, lat, lng);
                        });

                        // Update inputs on map click
                        map.addListener("click", function(event) {
                            const lat = event.latLng.lat();
                            const lng = event.latLng.lng();
                            marker.setPosition(event.latLng);
                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;
                            geocodeLatLng(geocoder, lat, lng);
                        });

                        // Initialize the autocomplete input
                        const input = document.getElementById('address-input');
                        autocomplete = new google.maps.places.Autocomplete(input);

                        // Bind the map's bounds (viewport) to the autocomplete object
                        autocomplete.bindTo('bounds', map);

                        // Set the data fields to return when the user selects a place
                        autocomplete.setFields(['address_components', 'geometry', 'name']);

                        // When a user selects a place from the search box
                        autocomplete.addListener('place_changed', function() {
                            const place = autocomplete.getPlace();
                            if (!place.geometry) {
                                console.error("Place has no geometry");
                                return;
                            }

                            // If the place has a geometry, move the marker and the map
                            if (place.geometry.viewport) {
                                map.fitBounds(place.geometry.viewport);
                            } else {
                                map.setCenter(place.geometry.location);
                                map.setZoom(17);  // Zoom in on the place
                            }

                            // Move the marker to the new location
                            marker.setPosition(place.geometry.location);

                            // Update the latitude and longitude inputs
                            document.getElementById('latitude').value = place.geometry.location.lat();
                            document.getElementById('longitude').value = place.geometry.location.lng();

                            // Update the address input
                            geocodeLatLng(geocoder, place.geometry.location.lat(), place.geometry.location.lng());
                        });
                    }

                    // Function to get the address from latitude/longitude
                    function geocodeLatLng(geocoder, lat, lng) {
                        const latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };
                        geocoder.geocode({ location: latlng }, function(results, status) {
                            if (status === 'OK') {
                                if (results[0]) {
                                    const address = results[0].formatted_address;
                                    document.getElementById('address').value = address;
                                } else {
                                    console.error('No results found');
                                }
                            } else {
                                console.error('Geocoder failed due to: ' + status);
                            }
                        });
                    }
            </script>

            <!-- Google Maps API with Places library -->
            <script
              src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&callback=initMap"
              async defer>
            </script>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea class="summernote" name="description">{{$profile->description}}</textarea>
          </div>
          <div class="form-group">
            <label>Facebook</label>
            <input type="text" class="form-control" name="fb_link" value="{{$profile->fb_link}}">
          </div>
          <div class="form-group">
            <label>Twitter</label>
            <input type="text" class="form-control" name="tw_link" value="{{$profile->tw_link}}">
          </div>
          <div class="form-group">
            <label>Instagram</label>
            <input type="text" class="form-control" name="insta_link" value="{{$profile->insta_link}}">
          </div>
          <button type="submmit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection