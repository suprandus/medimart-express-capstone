@extends('frontend.dashboard.layouts.master')

@section('content')
<!-- Main Content -->
<section class="section">
  <div class="section-header">
    <h1>Pharmacy Verification</h1>
  </div>
  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            {!!@$content->content!!}
          </div>
          <div class="card-body">
            <form action="{{route('user.vendor-request.create')}}" method="POST" enctype="multipart/form-data">
              @csrf
              {{-- Below is for necessary document to apply as verified pharmacy --}}
              <div class="form-group">
                <label>Pharmacy Profile</label>
                <input type="file" name="shop_image" class="form-control">
              </div>
              <div class="form-group">
                <label>License</label>
                <input type="file" class="form-control" name="">
              </div>
              <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="shop_name" value="" placeholder="Pharmacy Name" required
                  autofocus>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="shop_email" value="" placeholder="Pharmacy Email"
                      required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" name="shop_phone" value="" placeholder="Pharmacy Phone"
                      required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="shop_address" value="" placeholder="Pharmacy Address"
                  required>
              </div>
              <div class="form-group">
                <label>More Information</label>
                <textarea name="about" class="form-control" placeholder="About You" required></textarea>
              </div>
              <button type="submmit" class="btn btn-primary">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection