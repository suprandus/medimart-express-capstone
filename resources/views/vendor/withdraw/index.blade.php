@extends('vendor.layouts.master')

@section('content')
<!--=============================
    DASHBOARD START
  ==============================-->
<section class="section">
  <div class="section-header">
    <h1>Withdraw</h1>
  </div>

  {{-- Cards Row --}}
  <div class="row">
    {{-- Current Balance --}}
    <div class="col-lg-4 col-md-6 col-sm-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Current Balance</h4>
            </div>
            <div class="card-body">
              {{ $settings->currency_icon }}{{ number_format($currentBalance, 2) }}
            </div>
          </div>
        </div>
      </a>
    </div>

    {{-- Pending Amount --}}
    <div class="col-lg-4 col-md-6 col-sm-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-hourglass-half"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Pending Amount</h4>
            </div>
            <div class="card-body">
              {{ $settings->currency_icon }}{{number_format($pendingAmount, 2)}}
            </div>
          </div>
        </div>
      </a>
    </div>

    {{-- Total Withdraw --}}
    <div class="col-lg-4 col-md-6 col-sm-12">
      <a href="{{route('vendor.orders.index')}}" class="card-link">
        <div class="card card-statistic-1">
          <div class="card-icon bg-danger">
            <i class="fas fa-hand-holding-usd"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Amount</h4>
            </div>
            <div class="card-body">
              {{ $settings->currency_icon }}{{ number_format($pendingAmount, 2) }}
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>

  {{-- Data Table --}}
  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>All Withdraw</h4>
            <div class="card-header-action">
              <a href="{{route('vendor.withdraw.create')}}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Request
              </a>
            </div>
          </div>
          <div class="card-body">
            {{ $dataTable->table() }}
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
  {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
  @endpush