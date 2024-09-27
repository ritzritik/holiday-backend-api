@extends('layouts.admin.master')

@section('title', 'Create User - Sky Sea')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add Users</h1>
    <a href="{{ route('admin.user.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm text-white-50"></i> View All Users
    </a>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="alert-container"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form id="user-form" class="card shadow rounded p-3" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name">Name</label>
                    <input class="form-control" type="text" name="name" id="name" required>
                </div>
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input class="form-control" type="email" name="email" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="username">Username</label>
                    <input class="form-control" type="email" name="username" id="username" required>
                </div>
                <div class="mb-3">
                    <label for="password">Create Password</label>
                    <input class="form-control" type="password" name="password" id="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password">Confirm Password</label>
                    <input class="form-control" type="password" name="password_confirmation" id="confirm_password"  
                   required>
                  </div>
                <div class="mb-3">
                    <label for="user_type">User Type</label>
                    <select class="form-control" name="user_type" id="user_type" required>
                        <option value="">Select User Type</option>
                        <option value="1">Admin</option>
                        <option value="2">Management</option>
                        <option value="3">Author</option>
                        <option value="0">Normal User</option>
                    </select>
                </div>
                <div class="mb-3 p-3">
                    <button class="btn btn-primary" type="submit">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

<script>
    $('#user-form').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('admin.user.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(response) {
                toastr.success(response.success);
                $('#user-form')[0].reset();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                if(errors.email) {
                    toastr.error(errors.email[0]);
                    $('#email').addClass('is-invalid');
                } else if (errors.password) {
                    toastr.error(errors.password[0]);
                    $('#password').addClass('is-invalid');
                    $('#confirm_password').addClass('is-invalid');
                } else {
                    $('#email').removeClass('is-invalid');
                    $('#confirm_password').removeClass('is-invalid');
                }
            }
        });
    });
</script>
@endsection
