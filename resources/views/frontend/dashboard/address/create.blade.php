@extends('frontend.dashboard.layouts.master')

@section('content')
<!-- Main Content -->
<section class="section">
  <div class="section-header">
    <h1>Edit Address</h1>
  </div>
  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <form action="{{route('user.address.store')}}" method="POST">
              @csrf
              <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" value="" placeholder="Name" required>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="" placeholder="Email" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" name="phone" value="" placeholder="Phone" required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Country</label>
                <select class="form-control" name="country" required>
                  <option value="">Select Country</option>
                  @foreach (config('settings.country_list') as $country)
                  <option value="{{$country}}">{{$country}}</option>
                  @endforeach
                </select>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>State</label>
                    <input type="text" class="form-control" name="state" value="" placeholder="State" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>City</label>
                    <input type="text" class="form-control" name="city" value="" placeholder="City" required>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Zip Code</label>
                    <input type="text" class="form-control" name="zip" value="" placeholder="Zip Code" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Address</label>
                    <input type="text" class="form-control" name="address" value="" placeholder="Address" required>
                  </div>
                </div>
              </div>
              <button type="submmit" class="btn btn-primary">Save Changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection