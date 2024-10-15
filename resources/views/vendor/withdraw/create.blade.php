@extends('vendor.layouts.master')

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<div class="section">
  <div class="section-header">
    <h1>Create Request</h1>
  </div>
  <div class="section-body">
    <div class="card">
      <div class="card-body">
        <form action="{{route('vendor.withdraw.store')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <!-- Method Selection -->
          <div class="form-group">
            <label for="method" class="form-label">Mode of Payment</label>
            <select name="method" id="method" class="form-control">
              <option value="">Select</option>
              @foreach ($methods as $method)
              <option value="{{ $method->id }}">{{ $method->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Withdraw Amount -->
          <div class="form-group">
            <label for="amount" class="form-label">Withdraw Amount</label>
            <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter amount" min="0"
              step="0.01" required>
          </div>

          <!-- Account Information -->
          <div class="form-group">
            <label for="account_info" class="form-label">Account Information</label>
            <textarea name="account_info" id="account_info" class="form-control" rows="4"
              placeholder="Enter account details" required></textarea>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn btn-primary">Create</button>
        </form>
        <hr class="mt-4">
        <div class="card-body account_info_area">
          <!-- This area will be dynamically filled -->
        </div>
      </div>
    </div>
  </div>
</div>
</div>

<!--=============================
    DASHBOARD END
  ==============================-->
@endsection

@push('scripts')
<script>
  $(document).ready(function(){
            $('#method').on('change', function(e){
                let id = $(this).val();
                $.ajax({
                    method: 'GET',
                    url: "{{ route('vendor.withdraw.show', ':id') }}".replace(':id', id),
                    success: function(response){
                        $('.account_info_area').html(`
                    <h3>Payout range: {{ $settings->currency_icon }}${response.minimum_amount} - {{ $settings->currency_icon }}${response.maximum_amount}</h3>
                    <h3>Withdraw charge: ${response.withdraw_charge}%</h3>
                    <p>${response.description}</p>`)
                    },
                    error: function(error){
                        console.log(error);
                    }
                })
            });
        })
</script>
@endpush