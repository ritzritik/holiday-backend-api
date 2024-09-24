@extends('layouts.admin.master')

@section('title', 'Create Coupon')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Coupon</h1>
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
                <form action="{{ url('/admin/coupon/create') }}" method="post" class="card shadow rounded p-3">
                    @csrf
                    <div class="mb-3">
                        <label for="coupon_code">Coupon Code</label>
                        <input class="form-control" type="text" name="coupon_code" id="coupon_code" required style="text-transform: uppercase;">
                    </div>
                    <div class="mb-3">
                        <label for="discount">Discount</label><span class="percent-sign"> (in %)</span>
                        <input class="form-control" type="number" name="discount" id="discount" required>
                    </div>
                    <div class="mb-3">
                        <label for="active">Active Status</label>
                        <select class="form-control" name="active" id="active" required>
                            <option value="">Select Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="expiry_date">Expiry Date</label>
                        <input class="form-control" type="date" name="expiry_date" id="expiry_date" required>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary" type="submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
