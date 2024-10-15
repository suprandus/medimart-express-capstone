@extends('vendor.layouts.master')

{{-- @section('title')
{{$settings->site_name}} || Product Variant
@endsection --}}

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<div class="section">
  <div class="section-header">
    <h1>Product Variant</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-header">
        <h4>Product: {{$product->name}}</h4>
        <div class="card-header-action">
          <a href="{{route('vendor.products-variant.create', ['product' => $product->id])}}" class="btn btn-primary"><i
              class="fas fa-plus"></i> Create Variant</a>
        </div>
      </div>
      <div class="card-body">
        {{ $dataTable->table() }}
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

<script>
  $(document).ready(function(){
            $('body').on('click', '.change-status', function(){
                let isChecked = $(this).is(':checked');
                let id = $(this).data('id');

                $.ajax({
                    url: "{{route('vendor.products-variant.change-status')}}",
                    method: 'PUT',
                    data: {
                        status: isChecked,
                        id: id
                    },
                    success: function(data){
                        toastr.success(data.message)
                    },
                    error: function(xhr, status, error){
                        console.log(error);
                    }
                })

            })
        })
</script>
@endpush