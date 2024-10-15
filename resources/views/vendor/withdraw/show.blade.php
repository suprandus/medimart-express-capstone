@extends('vendor.layouts.master')

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<section class="section">
  <div class="section-header">
    <h1>Withdraw Request</h1>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-xl-9 col-xxl-10 col-lg-9 ms-auto">
          <div class="dashboard_content mt-2 mt-md-0">
            <div class="">
              <div class="row">
                <div class="">
                  <table class="table">
                    <tbody>
                      <tr>
                        <td><b>Withdraw Method:</b></td>
                        <td>{{ $request->method }}</td>
                      </tr>
                      <tr>
                        <td><b>Withdraw Charge:</b></td>
                        <td>{{ ($request->withdraw_charge / $request->total_amount) * 100 }} %</td>
                      </tr>
                      <tr>
                        <td><b>Withdraw Charge Amount:</b></td>
                        <td>{{ $settings->currency_icon }} {{ $request->withdraw_charge }}</td>
                      </tr>
                      <tr>
                        <td><b>Total Amount:</b></td>
                        <td>{{ $settings->currency_icon }} {{ $request->total_amount }}</td>
                      </tr>
                      <tr>
                        <td><b>Withdraw Amount:</b></td>
                        <td>{{ $settings->currency_icon }} {{ $request->withdraw_amount }}</td>
                      </tr>
                      <tr>
                        <td><b>Status:</b></td>
                        <td>
                          @if ($request->status == 'pending')
                          <span class="badge bg-warning">Pending</span>
                          @elseif($request->status == 'paid')
                          <span class="badge bg-success">Paid</span>
                          @else
                          <span class="badge bg-danger">Declined</span>
                          @endif
                        </td>
                      </tr>
                      <tr>
                        <td><b>Account Information:</b></td>
                        <td>{!! $request->account_info !!}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!--=============================
    DASHBOARD END
  ==============================-->
@endsection