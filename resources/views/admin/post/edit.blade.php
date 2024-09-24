@extends('layouts.admin.master')

@section('content')
<div class="container">
    <h1>Edit Post</h1>
    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $post->title }}" required>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ $post->content }}</textarea>
        </div>

        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" class="form-control" id="author" name="author" value="{{ $post->author }}" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending" {{ $post->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $post->status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $post->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>
@endsection
