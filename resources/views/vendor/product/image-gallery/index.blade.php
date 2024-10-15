@extends('vendor.layouts.master')

{{-- @section('title')
{{$settings->site_name}} || Image Gallery
@endsection --}}

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<div class="section">
  <div class="section-header">
    <h1>Image Gallery</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4>Product Images</h4>
        <div class="card-header-action">
          <a href="{{route('vendor.products.index')}}" class="btn btn-warning"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{route('vendor.products-image-gallery.store')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label for="">Image <code>(Multiple image supported!)</code></label>
            <input type="file" name="image[]" class="form-control" multiple>
            <input type="hidden" name="product" value="{{$product->id}}">
          </div>
          <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        <hr class="mt-4">
        <div class="card-body">
          {{ $dataTable->table() }}
        </div>
      </div>
    </div>
  </div>
</div>
<!--=============================
    DASHBOARD START
  ==============================-->
@endsection

@push('scripts')
{{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush