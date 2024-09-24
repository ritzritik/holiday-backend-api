@extends('layouts.admin.master')

@section('content')
<div class="container">
    <h1 class="my-4">Draft Testimonials</h1>
    <a href="{{ route('admin.testimonial.create') }}" class="btn btn-primary mb-3">Add New Testimonial</a>

    @if(session('success'))
        <div class="alert alert-success" id="alert">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger" id="alert">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Text</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($testimonials as $testimonial)
                <tr>
                    <td>{{ $testimonial->name }}</td>
                    <td>{{ $testimonial->text }}</td>
                    <td>
                        @if($testimonial->image)
                            <img src="{{ asset('storage/' . $testimonial->image) }}" alt="{{ $testimonial->name }}"
                                style="max-width: 80px;">
                        @else
                            No image
                        @endif
                    </td>
                    <td>{{ $testimonial->status }}</td>
                    <td>
                        <!-- Edit button to go to edit page -->
                        <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}"
                            class="btn btn-warning btn-sm">Edit</a>

                        <!-- Form to publish the draft testimonial -->
                        <form
                            action="{{ route('testimonial.changeStatus', ['id' => $testimonial->id, 'status' => 'published']) }}"
                            method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">Publish</button>
                        </form>

                        <!-- Optionally delete the testimonial -->
                        <form action="{{ route('admin.testimonials.destroy', $testimonial->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection