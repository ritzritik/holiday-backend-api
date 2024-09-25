@extends('layouts.admin.master')

@section('title', 'Manage Parking Prices')

@section('content')

<div class="container">
    <h1>Manage Parking Prices</h1>

    <!-- Form to Set or Update Prices -->
    <form id="priceForm" method="POST" action="{{ route('admin.setPricing') }}">
        @csrf

        <div class="row">
            <!-- Airport Search/Selection (Column 1) -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="airport">Search/Select Airport</label>
                    <select id="airport" class="form-control" name="airport_id">
                        <option value="">Select an Airport</option>
                        @foreach($airports as $airport)
                            <option value="{{ $airport->id }}">{{ $airport->name }} ({{ $airport->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Private Parking Price (Column 2) -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="private_parking_price">Private Parking Price</label>
                    <input type="number" step="0.01" class="form-control" id="private_parking_price" name="private_parking_price" placeholder="Enter price for private parking">
                </div>
            </div>

            <!-- Standard Parking Price (Column 3) -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="standard_parking_price">Standard Parking Price</label>
                    <input type="number" step="0.01" class="form-control" id="standard_parking_price" name="standard_parking_price" placeholder="Enter price for standard parking">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save Prices</button>
    </form>

    <!-- Search Box for Filtering Data -->
    <div class="mt-4">
        <input type="text" id="searchInput" class="form-control" placeholder="Search saved records (by airport name)">
    </div>

    <!-- Table for Displaying Saved Data -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered" id="parkingDataTable">
            <thead>
                <tr>
                    <th>Airport Name</th>
                    <th>Private Parking Price</th>
                    <th>Standard Parking Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pricingData as $pricing)
                <tr>
                    <td>{{ $pricing->airport->name }} ({{ $pricing->airport->code }})</td>
                    <td>{{ $pricing->private_parking_price }}</td>
                    <td>{{ $pricing->standard_parking_price }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $pricingData->links() }} <!-- Laravel Pagination -->
    </div>
</div>

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    $(document).ready(function() {
        console.log('Document is ready');

        // Initialize Select2 for the airport dropdown
        $('#airport').select2();

        // When an airport is selected, fetch pricing info via AJAX
        $('#airport').on('change', function() {
            let airportId = $(this).val();
            if (airportId) {
                // Fetch existing prices via AJAX
                $.ajax({
                    url: '/admin/getPricing/' + airportId,
                    type: 'GET',
                    success: function(response) {
                        if (response.exists) {
                            $('#private_parking_price').val(response.private_parking_price);
                            $('#standard_parking_price').val(response.standard_parking_price);
                        } else {
                            // Clear the fields if no prices exist
                            $('#private_parking_price').val('');
                            $('#standard_parking_price').val('');
                        }
                    },
                    error: function() {
                        alert('Error fetching data. Please try again.');
                    }
                });
            } else {
                // Clear the price fields if no airport is selected
                $('#private_parking_price').val('');
                $('#standard_parking_price').val('');
            }
        });

        // JavaScript Search Algorithm for Saved Data
        $('#searchInput').on('keyup', function() {
            console.log('Search input changed'); // Debugging statement
            let value = $(this).val().toLowerCase();
            console.log('Search value:', value); // Debugging statement
            $('#parkingDataTable tbody tr').each(function() {
                let rowText = $(this).text().toLowerCase();
                console.log('Row text:', rowText); // Debugging statement
                $(this).toggle(rowText.indexOf(value) > -1);
            });
        });
    });
</script>


@endsection
