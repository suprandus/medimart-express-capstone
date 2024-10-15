@extends('vendor.layouts.master')

@section('title')
{{$settings->site_name}} || Product Variant
@endsection

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<div class="section">
  <div class="section-header">
    <h1>Create Variant</h1>
  </div>

  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <form action="{{route('vendor.products-variant.store')}}" method="POST">
          @csrf
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name" value="">
          </div>
          <div class="form-group">
            <input type="hidden" class="form-control" name="product" value="{{request()->product}}">
          </div>
          <div class="form-group">
            <label for="inputState">Status</label>
            <select id="inputState" class="form-control" name="status">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
          <button type="submmit" class="btn btn-primary">Create</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!--=============================
    DASHBOARD START
  ==============================-->
@endsection