@extends('layouts.admin.master')

@section('title', 'Edit Coupon')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Coupon</h1>
        <a href="{{ url('/admin/coupon') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Coupons
        </a>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form action="{{ url('/admin/coupon/' . $coupon->coupon_id) }}" method="post" class="card shadow rounded p-3">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="coupon_code">Coupon Code</label>
                        <input class="form-control" type="text" name="coupon_code" id="coupon_code" value="{{ $coupon->coupon_code }} %" required>
                    </div>
                    <div class="mb-3">
                        <label for="discount">Discount</label>
                        <input class="form-control" type="number" name="discount" id="discount" value="{{ $coupon->discount }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="active_status">Active Status</label>
                        <select class="form-control" name="active_status" id="active_status" required>
                            <option value="">Select Status</option>
                            <option value="1" {{ $coupon->active_status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$coupon->active_status ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="expiry_date">Expiry Date</label>
                        <input class="form-control" type="date" name="expiry_date" id="expiry_date" value="{{ $coupon->expiry_date }}" required>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
