@extends('layouts.admin.master')

@section('content')
<div class="container">
    <h1 class="my-4">Create New Testimonial</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form id="testimonialForm" action="{{ route('admin.testimonials.store') }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="text">Text</label>
            <textarea name="text" id="text" class="form-control @error('text') is-invalid @enderror" rows="4"
                required>{{ old('text') }}</textarea>
            @error('text')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">Image (Optional)</label>
            <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror"
                accept="image/*" onchange="previewImage(event)">
            <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 150px; display:none;" class="mt-2">
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Buttons for saving testimonial -->
        <button type="button" onclick="submitForm('published')" class="btn btn-primary">Publish Now</button>
        <button type="button" onclick="submitForm('draft')" class="btn btn-secondary">Save as Draft</button>
    </form>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function () {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    function submitForm(status) {
        let form = document.getElementById('testimonialForm');
        let statusInput = document.createElement('input');
        statusInput.setAttribute('type', 'hidden');
        statusInput.setAttribute('name', 'status');
        statusInput.setAttribute('value', status);
        form.appendChild(statusInput);
        form.submit();
    }
</script>
@endsection