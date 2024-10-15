@extends('vendor.layouts.master')

{{-- @section('title')
{{$settings->site_name}} || Product Variant Item
@endsection --}}

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<div class="section">
  <div class="section-header">
    <h1>Product Variant Item</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4></h4>
        <div class="card-header-action">
          <a href="{{route('vendor.products-variant-item.index',
            ['productId' => $product->id, 'variantId' => $variant->id])}}" class="btn btn-warning"><i
              class="fas fa-arrow-left"></i>
            Back</a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{route('vendor.products-variant-item.store')}}" method="POST">
          @csrf

          <div class="form-group">
            <label>Variant Name</label>
            <input type="text" class="form-control" name="variant_name" value="{{$variant->name}}" readonly>
          </div>

          <div class="form-group">
            <input type="hidden" class="form-control" name="variant_id" value="{{$variant->id}}">
          </div>
          <div class="form-group">
            <input type="hidden" class="form-control" name="product_id" value="{{$product->id}}">
          </div>

          <div class="form-group">
            <label>Item Name</label>
            <input type="text" class="form-control" name="name" value="">
          </div>

          <div class="form-group">
            <label>Price <code>(Set 0 for make it free)</code></label>
            <input type="text" class="form-control" name="price" value="">
          </div>

          <div class="form-group">
            <label for="inputState">Is Default</label>
            <select id="inputState" class="form-control" name="is_default">
              <option value="">Select</option>
              <option value="1">Yes</option>
              <option value="0">No</option>
            </select>
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