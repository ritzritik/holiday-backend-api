@extends('layouts.admin.master')

@section('title', 'Manage Coupon')

@section('custom_style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Coupons</h1>
        <a href="{{ url('/admin/coupon/create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Coupon
        </a>
    </div>

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="p-3 card shadow rounded">
                    <table id="couponTable" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Coupon ID </th>
                                <th>Coupon Code </th>
                                <th>Discount </th>
                                <th>Active Status </th>
                                <th>Expiry Date </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->coupon_id }}</td>
                                    <td>{{ $coupon->coupon_code }}</td>
                                    <td>{{ $coupon->discount }} %</td>
                                    <td>{{ $coupon->active ? 'Active' : 'Inactive' }}</td>
                                    <td>{{ $coupon->expiry_date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ url('/admin/coupon/' . $coupon->coupon_id . '/edit') }}"
                                            class="btn btn-sm btn-info"><i class="fas fa-pen"></i> Edit</a>
                                        <a href="#" data-id="{{ $coupon->coupon_id }}"
                                            data-url="{{ url('/admin/coupon/delete/' . $coupon->coupon_id) }}"
                                            class="btn btn-sm btn-danger delete-btn">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    It will go to trash. Are you sure you want to delete this coupon?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
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
        $(document).ready(function() {
            var table = new DataTable('#couponTable', {
                "order": [],
                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4],
                    "orderable": true
                }]
            });

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var url = $(this).data('url');
                $('#deleteForm').attr('action', url);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@endsection
