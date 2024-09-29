@extends('layouts.admin.master')

@section('title', 'Manage Parking Prices')

@section('custom_style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.1/css/dataTables.bootstrap5.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manage Parking Prices</h1>
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
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="p-3 card shadow rounded">
                    <form id="priceForm" method="POST" action="{{ route('admin.setPricing') }}">
                        @csrf
                        <div class="row mb-3">
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

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="private_parking_price">Private Parking Price</label>
                                    <input type="number" step="0.01" class="form-control" id="private_parking_price" name="private_parking_price" placeholder="Enter price for private parking">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="standard_parking_price">Standard Parking Price</label>
                                    <input type="number" step="0.01" class="form-control" id="standard_parking_price" name="standard_parking_price" placeholder="Enter price for standard parking">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Save Prices</button>
                    </form>

                    <!-- Table for Displaying Saved Data -->
                    <div class="table-responsive mt-4">
                        <table id="parkingDataTable" class="table table-striped" style="width:100%">
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
            </div>
        </div>
    </div>

@endsection

@section('custom_script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Use a stable version -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script> <!-- Include Select2 JS -->

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#parkingDataTable').DataTable();

            // Initialize Select2 for the airport dropdown
            $('#airport').select2();

            // Search functionality
            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Fetch existing prices on airport selection
            $('#airport').on('change', function() {
                let airportId = $(this).val();
                if (airportId) {
                    $.ajax({
                        url: '/admin/getPricing/' + airportId,
                        type: 'GET',
                        success: function(response) {
                            if (response.exists) {
                                $('#private_parking_price').val(response.private_parking_price);
                                $('#standard_parking_price').val(response.standard_parking_price);
                            } else {
                                $('#private_parking_price').val('');
                                $('#standard_parking_price').val('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            alert('Error fetching data. Please try again.');
                        }
                    });
                } else {
                    $('#private_parking_price').val('');
                    $('#standard_parking_price').val('');
                }
            });
        });
    </script>
@endsection
