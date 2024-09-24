@extends('layouts.admin.master')

@section('title', 'Trasfer Details')

@section('content')

<?php $i=1; ?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mx-auto text-primary">Registered Users </h1>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="p-3 card shadow rounded">
                <table id="userTable" class="table table-striped " style="width:100%">
                    <thead>
                        <tr>
                            <th>Sno.</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Registered At</th>
                            <th>Send Deals</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($regis_users as $user)
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                {{-- <td><img src="{{asset('uploads/user/'.$user->name)}}" alt="" height="32"></td> --}}
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td style="padding-left:41px">
                                    <a href="#" class="btn btn-sm btn-info send-email-btn" data-toggle="modal"
                                        data-target="#emailModal" data-email="{{ $user->email }}"><i class="fas fa-paper-plane"></i></a>
                                    {{-- <a href="{{ url('admin/user/edit/' . $user->id) }}" class="btn btn-info"><i class="fas fa-pen"></i> Edit</a> --}}
                                    {{-- <a href="#"><i class="fa fa-paper-plane"></i></a></td> --}}
                            </tr>
                            <?php $i++; ?>
                        @endforeach

                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Send Deals</h5>
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
                    {{-- <div class="form-group">
                        <label for="emailSubject">Subject:</label>
                        <input type="text" class="form-control" id="emailSubject" required>
                    </div> --}}
                    <div class="form-group">
                        <label for="emailMessage">Offers:</label>
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
