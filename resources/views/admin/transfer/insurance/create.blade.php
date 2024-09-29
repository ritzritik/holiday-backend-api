@extends('layouts.admin.master')

@section('title', 'Create Insurance Plan')

@section('content')
<div class="container">
    <h1>Create Insurance Plan</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.insurance.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="plan_name" class="form-label">Plan Name</label>
            <input type="text" class="form-control" id="plan_name" name="plan_name" required>
        </div>
        <div class="mb-3">
            <label for="premium_amount" class="form-label">Price</label>
            <input type="number" class="form-control" id="premium_amount" name="premium_amount" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="coverage_details" class="form-label">Coverage</label>
            <input type="text" class="form-control" id="coverage_details" name="coverage_details" required>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration (in yrs)</label>
            <input type="number" class="form-control" id="duration" name="duration" required>
        </div>
        <div class="mb-3">
            <label for="active" class="form-label">Active Status</label>
            <select class="form-control" id="active" name="active" required>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
