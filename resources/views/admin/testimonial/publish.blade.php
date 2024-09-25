@extends('layouts.admin.master')

@section('content')
<div class="container">
    <h1 class="my-4">Published Testimonials</h1>
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
                <th>Published At</th>
                <th>Published By</th>
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
                    <td>{{ $testimonial->created_at->format('d/m/Y')}}</td>
                    <td>{{ $testimonial->created_by}}</td>
                    <td>
                        <!-- Edit button to go to edit page -->
                        <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}"
                            class="btn btn-success btn-sm">Edit</a>

                        <!-- Form to toggle testimonial status to draft -->
                        <form
                            action="{{ route('testimonial.changeStatus', ['id' => $testimonial->id, 'status' => 'draft']) }}"
                            method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm">Move to Draft</button>
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

<script>
    // Hide the alert after 5 seconds (5000 milliseconds)
    setTimeout(function () {
        let successAlert = document.getElementById('alert');
        if (successAlert) {
            successAlert.style.display = 'none';
        }
    }, 2000);
</script>