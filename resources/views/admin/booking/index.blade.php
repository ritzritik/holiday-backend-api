@extends('layouts.admin.master')

@section('title', 'Booking Details')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800">Booking Status</h1>
    <div class="row">
        <div class="col-md-3">
            <button class="btn btn-success btn-block load-data" data-url="{{ url('/admin/booked/packages') }}">Packages</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary btn-block load-data" data-url="{{ url('/admin/booked/flights') }}">Flights</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-warning btn-block load-data" data-url="{{ url('/admin/booked/hotels') }}">Hotels</button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-info btn-block load-data" data-url="{{ url('/admin/booked/holidays') }}">Holidays</button>
        </div>
    </div>

    <div id="booking-content" class="mt-4">
        <!-- Content will be loaded here -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('.load-data').click(function (event) {
            event.preventDefault();
            var url = $(this).data('url');
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#booking-content').html(response);
                },
                error: function () {
                    alert('Failed to load data');
                }
            });
        });
    });
</script>

@endsection
