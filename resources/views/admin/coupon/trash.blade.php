@extends('layouts.admin.master')

@section('title', 'Trash - Coupons')

@section('custom_style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">
@endsection

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Trash Coupons</h1>
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
                <div class="p-3 card shadow rounded">
                    <table id="couponTable" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Coupon Code</th>
                                <th>Discount</th>
                                <th>Expiry Date</th>
                                <th>Deleted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trashed_coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->coupon_code }}</td>
                                    <td>{{ $coupon->discount }} %</td>
                                    <td>{{ $coupon->expiry_date->format('d/m/Y') }}</td>
                                    <td>{{ $coupon->updated_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="#" data-id="{{ $coupon->id }}"
                                            data-url="{{ url('/coupon/trash/restore/' . $coupon->id) }}"
                                            class="btn btn-success restore-btn">
                                            <i class="fas fa-trash-restore"></i>
                                        </a>
                                        <a href="#" data-id="{{ $coupon->id }}"
                                            data-url="{{ url('/admin/trash/coupon/delete/' . $coupon->id) }}"
                                            class="btn btn-danger delete-btn">
                                            <i class="fas fa-trash-alt"></i>
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

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-labelledby="restoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restoreModalLabel">Confirm Restore</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to restore this coupon?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="restoreForm" method="POST" action="">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">Restore</button>
                    </form>
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
                    Are you sure you want to delete this coupon permanently?
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
        new DataTable('#couponTable');

        $(document).on('click', '.restore-btn', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            $('#restoreForm').attr('action', url); // Set the form's action URL to the restore URL
            $('#restoreModal').modal('show'); // Show the restore modal
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            $('#deleteForm').attr('action', url); // Set the form's action URL to the delete URL
            $('#deleteModal').modal('show'); // Show the delete modal
        });
    </script>
@endsection
