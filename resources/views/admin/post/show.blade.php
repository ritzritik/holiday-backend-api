@extends('layouts.admin.master')

@section('content')
<div class="container">
    <h1>{{ $post->title }}</h1>
    <p><strong>Author:</strong> {{ $post->author }}</p>
    <p><strong>Status:</strong> {{ ucfirst($post->status) }}</p>
    <div>
        <strong>Content:</strong>
        <p>{{ $post->content }}</p>
    </div>

    <div class="d-flex mb-3">
        <form action="{{ route('admin.posts.approve', $post->id) }}" method="POST" style="margin-right: 10px;">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-success">Approve</button>
        </form>

        <form action="{{ route('admin.posts.reject', $post->id) }}" method="POST" style="margin-right: 10px;">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-warning">Reject</button>
        </form>
    </div>

    <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Back to Posts</a>
</div>
@endsection
