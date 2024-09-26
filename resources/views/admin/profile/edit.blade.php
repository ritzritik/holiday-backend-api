<!-- resources/views/profile/edit.blade.php -->

@extends('layouts.admin.master')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Edit Profile</h1>

    @if(session('success'))
        <div class="bg-green-500 text-white p-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium">Name</label>
            <input type="text" name="name" id="name" value="{{ $user->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="username" class="block text-sm font-medium">Username</label>
            <input type="text" name="username" id="username" value="{{ $user->username }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium">Email</label>
            <input type="email" name="email" id="email" value="{{ $user->email }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>

        <div class="mb-4">
            <label for="phone_number" class="block text-sm font-medium">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" value="{{ $user->phone_number }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <div class="mb-4">
            <label for="profile_pic" class="block text-sm font-medium">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic" class="mt-1 block w-full">
        </div>

        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Profile</button>
        </div>
    </form>
</div>
@endsection
