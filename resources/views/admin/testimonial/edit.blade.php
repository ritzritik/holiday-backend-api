@extends('layouts.admin.master')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Testimonial</h1>

    <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $testimonial->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="text">Text</label>
            <textarea name="text" id="text" class="form-control @error('text') is-invalid @enderror" rows="4" required>{{ old('text', $testimonial->text) }}</textarea>
            @error('text')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">Image (Optional)</label>
            <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" onchange="previewImage(event)">
            @if($testimonial->image)
                <img src="{{ asset('storage/' . $testimonial->image) }}" alt="Current Image" class="mt-2" style="max-width: 150px;" id="imagePreview">
            @endif
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="draft" {{ old('status', $testimonial->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $testimonial->status) == 'published' ? 'selected' : '' }}>Published</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
