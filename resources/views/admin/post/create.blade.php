@extends('layouts.admin.master')

@section('title', 'Users - Sky Sea')

@section('custom_style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">

@endsection

@section('content')
<div class="container">
    <h1>Create Post</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>
        </div>

        <div class="form-group">
            <label for="author">Author</label>
            <input type="text" class="form-control" id="author" name="author" value="{{ old('author') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Post</button>
    </form>
</div>
@endsection
