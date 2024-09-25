@extends('layouts.admin.master')

@section('title', 'Posts')

@section('content')
<div class="container">
    <h1>Posts Management</h1>
    @if(session('success'))
        <div class="alert alert-success" id="alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="post-list">
            @foreach($posts as $post)
            <tr id="post-{{ $post->id }}" style="cursor:pointer;">
                <td>{{ $post->id }}</td>
                <td>{{ $post->title }}</td>
                <td>{{ $post->author }}</td>
                <td id="status-{{ $post->id }}">{{ ucfirst($post->status) }}</td>
                <td>
                    <button class="btn btn-primary edit-post" data-id="{{ $post->id }}">Edit</button>
                    <button class="btn btn-danger delete-post" data-id="{{ $post->id }}">Delete</button>
                    <button class="btn btn-success approve-post" data-id="{{ $post->id }}">Approve</button>
                    <button class="btn btn-warning reject-post" data-id="{{ $post->id }}">Reject</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {

        // Function to handle fading out the alert
        setTimeout(function() {
            $('#alert-success').fadeOut('slow');
        }, 2000);

        // Approve post
        $('.approve-post').click(function() {
            let postId = $(this).data('id');
            $.ajax({
                url: '{{ url("admin/post") }}/' + postId + '/approve',
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#status-' + postId).text('Approved');
                    showAlert('Post approved successfully.');
                }
            });
        });

        // Reject post
        $('.reject-post').click(function() {
            let postId = $(this).data('id');
            $.ajax({
                url: '{{ url("admin/post") }}/' + postId + '/reject',
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#status-' + postId).text('Rejected');
                    showAlert('Post rejected successfully.');
                }
            });
        });

        // Delete post
        $('.delete-post').click(function() {
            if(confirm('Are you sure you want to delete this post?')) {
                let postId = $(this).data('id');
                $.ajax({
                    url: '{{ url("admin/post") }}/' + postId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#post-' + postId).remove();
                        showAlert('Post deleted successfully.');
                    }
                });
            }
        });

        // Edit post (this will require additional implementation for AJAX-based form handling)
        $('.edit-post').click(function() {
            let postId = $(this).data('id');
            window.location.href = '{{ url("admin/post") }}/' + postId + '/edit';
        });

        // Function to display alert
        function showAlert(message) {
            let alertBox = $('<div class="alert alert-success"></div>').text(message);
            $('body').prepend(alertBox);
            setTimeout(function() {
                alertBox.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 2000);
        }
    });
</script>
@endsection
