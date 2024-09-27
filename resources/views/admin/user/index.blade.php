@extends('layouts.admin.master')

@section('title', 'Users - Sky Sea')

@section('custom_style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">

@endsection

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">User</h1>
        @if (Auth::guard('admin')->user()->user_type == 1)
            <a href="{{ url('/admin/user/create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Add User
            </a>
        @endif
    </div>

    <div class="container">
        <div class="row" style="transition: opacity 2s ease-in-out;">
            <div class="col-md-12" style="transition: opacity 2s ease-in-out;">
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert"
                        style="transition: opacity 2s ease-in-out;">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="p-3 card shadow rounded">
                    <table id="userTable" class="table table-striped " style="width:100%">
                        <thead>
                            <tr>
                                <th>Sno.</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Create At</th>
                                <th>Create By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; ?>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    {{-- <td><img src="{{asset('uploads/user/'.$user->name)}}" alt="" height="32"></td> --}}
                                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $user->creator ? $user->creator->name : 'No user' }}</td>
                                    <td>
                                        <a href="{{ url('admin/user/edit/' . $user->id) }}" class="btn btn-info"><i class="fas fa-pen"></i> Edit</a>
                                        <a href="#" data-id="{{ $user->id }}"
                                            data-url="{{ url('admin/user/delete/' . $user->id) }}"
                                            class="btn btn-danger delete-btn"><i class="fas fa-trash-alt"></i> Delete</a>
                                    </td>
                                    <?php $i++; ?>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>
            </div>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('PATCH') <!-- Ensure the method is PATCH for updating -->
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('custom_script')

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.1/js/dataTables.bootstrap5.js"></script>

    <script>
        new DataTable('#userTable');
    </script>
@endsection
@section('scripts')
    <script>
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            $('#deleteForm').attr('action', url); // Set the form's action URL to the delete URL
            $('#deleteModal').modal('show'); // Show the modal
        });
    </script>
@endsection
