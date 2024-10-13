@extends('frontend.dashboard.layouts.master')

@section('content')
<!-- Main Content -->
<section class="section">
  <div class="section-header">
    <h1>Addresses</h1>
  </div>
  <div class="section-body">
    <div class="row">
      @foreach ($addresses as $address)
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">{{ $address->name }}</h5>
            <p class="card-text mb-1"><strong>Phone:</strong> {{ $address->phone }}</p>
            <p class="card-text mb-1"><strong>Email:</strong> {{ $address->email }}</p>
            <p class="card-text mb-1"><strong>Country:</strong> {{ $address->country }}</p>
            <p class="card-text mb-1"><strong>State:</strong> {{ $address->state }}</p>
            <p class="card-text mb-1"><strong>City:</strong> {{ $address->city }}</p>
            <p class="card-text mb-1"><strong>Zip Code:</strong> {{ $address->zip }}</p>
            <p class="card-text mb-3"><strong>Address:</strong> {{ $address->address }}</p>
            <div class="mt-0">
              <a href="{{ route('user.address.edit', $address->id) }}" class="btn btn-primary">Edit</a>
              <a href="{{ route('user.address.destroy', $address->id) }}" class="btn btn-danger delete-item">Delete</a>
            </div>
          </div>
        </div>
      </div>
      @endforeach
      <div class="col-12">
        <a href="{{ route('user.address.create') }}" class="btn btn-primary mt-0"><i class="fas fa-plus"></i>New
          Address</a>
      </div>
    </div>
  </div>
</section>
@endsection