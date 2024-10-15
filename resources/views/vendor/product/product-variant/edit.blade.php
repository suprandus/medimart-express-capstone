@extends('vendor.layouts.master')

{{-- @section('title')
{{$settings->site_name}} || Product Variant
@endsection --}}

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<section class="section">
  <div class="section-header">
    <h1>Update Variant</h1>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <form action="{{route('vendor.products-variant.update', $variant->id)}}" method="POST">
          @csrf
          @method('PUT')
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="{{$variant->name}}">
          </div>

          <div class="form-group">
            <label for="inputState">Status</label>
            <select id="inputState" class="form-control" name="status">
              <option {{$variant->status == 1 ? 'selected' : ''}} value="1">Active</option>
              <option {{$variant->status == 0 ? 'selected' : ''}} value="0">Inactive</option>
            </select>
          </div>
          <button type="submmit" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</section>
<!--=============================
    DASHBOARD START
  ==============================-->
@endsection