@extends('layouts.admin.master')

@section('title', 'Parking Details')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Subscribers</h1>
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
                            <th>Email ID</th>
                            <th>Date </th>
                            <th>Reply </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subscribers as $subscriber)
                            <tr>
                                <td>{{ $subscriber->email }}</td>
                                <td>{{ $subscriber->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-info send-email-btn" data-toggle="modal"
                                        data-target="#emailModal" data-email="{{ $subscriber->email }}"> <i class="fas fa-paper-plane"></i> Send</a>
                                    <a href="#" data-id="{{ $subscriber->id }}"
                                        data-url="{{ url('/admin/coupon/delete/' . $subscriber->id) }}"
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


<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Send Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="emailForm">
                    <div class="form-group">
                        <label for="recipientEmail">To:</label>
                        <input type="email" class="form-control" id="recipientEmail" readonly>
                    </div>
                    <div class="form-group">
                        <label for="emailSubject">Subject:</label>
                        <input type="text" class="form-control" id="emailSubject" required>
                    </div>
                    <div class="form-group">
                        <label for="emailMessage">Message:</label>
                        <textarea class="form-control" id="emailMessage" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.send-email-btn', function () {
        var email = $(this).data('email');
        $('#recipientEmail').val(email);
    });

    $('#emailForm').on('submit', function (e) {
        e.preventDefault();

        var emailData = {
            email: $('#recipientEmail').val(),
            subject: $('#emailSubject').val(),
            message: $('#emailMessage').val(),
        };

        $.ajax({
            url: '/admin/send-email',
            type: 'POST',
            data: emailData,
            success: function (response) {
                $('#emailModal').modal('hide');
                alert('Email sent successfully!');
            },
            error: function (response) {
                alert('Failed to send email.');
            }
        });
    });
</script>
@endsection