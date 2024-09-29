@extends('layouts.admin.master')

@section('title', 'Edit Insurance Plan')

@section('content')
<div class="container">
    <h1>Edit Insurance Plan</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.insurance.update', $insurancePlan->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Plan Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $insurancePlan->name }}" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" value="{{ $insurancePlan->price }}" required>
        </div>
        <div class="mb-3">
            <label for="coverage" class="form-label">Coverage</label>
            <input type="text" class="form-control" id="coverage" name="coverage" value="{{ $insurancePlan->coverage }}" required>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration (in days)</label>
            <input type="number" class="form-control" id="duration" name="duration" value="{{ $insurancePlan->duration }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
