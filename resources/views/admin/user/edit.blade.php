@extends('layouts.admin.master')

@section('title', 'Edit User - Sky Sea')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
        {{-- <a href="{{ route('admin.user.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user fa-sm text-white-50"></i> View All Users
        </a> --}}
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form action="{{ url('/admin/user/update/'.$user->id) }}" method="post" class="card shadow rounded p-3" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input class="form-control" type="text" name="name" id="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input class="form-control" type="email" name="email" id="email" value="{{ $user->email }}" style="cursor:not-allowed" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="username">Username</label>
                        <input class="form-control" type="text" name="username" id="username" value="{{ $user->username }}" style="cursor:not-allowed" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="password">Update Password</label>
                        <input class="form-control" type="password" name="password" id="password">
                        <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
                    </div>
                    <div class="mb-3">
                        <label for="user_type">User Type</label>
                        <select class="form-control" name="user_type" id="user_type" required>
                            <option value="1" {{ $user->user_type == 1 ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ $user->user_type == 2 ? 'selected' : '' }}>Management</option>
                            <option value="3" {{ $user->user_type == 3 ? 'selected' : '' }}>Author</option>
                            <option value="0" {{ $user->user_type == 0 ? 'selected' : '' }}>Normal User</option>
                        </select>
                    </div>
                    {{-- <div class="mb-3">
                        <label for="profile_picture">Profile Picture</label>
                        <input class="form-control" type="file" name="profile_picture" id="profile_picture">
                        @if($user->profile_picture)
                            <img src="{{ asset('uploads/user/'.$user->profile_picture) }}" alt="Profile Picture" height="200">
                        @endif
                    </div> --}}
                    <div class="mb-3 p-3">
                        <button class="btn btn-primary" type="submit">Update</button>
                        <button class="btn btn-secondary" type="submit">Cancel</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
