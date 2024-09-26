@extends('layouts..admin.master')

@section('content')
<div class="container mx-auto py-8">
    <div class="flex flex-col items-center">
        <img src="{{ $user->profile_pic }}" alt="{{ $user->name }}'s Profile Picture" class="rounded-full w-32 h-32 mb-4">
        <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
        <p class="text-gray-600">{{ $user->username }}</p>
        <p class="text-gray-600">{{ $user->email }}</p>
        <p class="text-gray-600">{{ $user->phone_number }}</p>

        <div class="mt-6">
            <h2 class="text-xl font-semibold">User Type: {{ ucfirst($user->user_type) }}</h2>
            <p>Status: {{ $user->is_active ? 'Active' : 'Inactive' }}</p>
            <p>Email Verified: {{ $user->email_verified ? 'Yes' : 'No' }}</p>
        </div>

        <div class="mt-8">
            <a href="{{ route('admin.profile.edit', $user->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Edit Profile</a>
        </div>
    </div>
</div>
@endsection
