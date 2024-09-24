<table class="table table-bordered">
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Package Type</th>
            <th>Total People</th>
            <th>Duration</th>
            <th>Booked On</th>
            <th>Booked By</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalPrice = 0; // Initialize the total price variable
        @endphp
        @foreach ($packages as $package)
            <tr>
                <td>{{ $package->booking_id }}</td>
                <td>{{ $package->package_type }}</td>
                <td>{{ $package->total_people }}</td> <!-- Display total passengers -->
                <td>{{ $package->duration }} days</td> <!-- Ensure duration is displayed -->
                <td>{{ $package->created_at ? $package->created_at->format('d/m/Y') : 'Date not available' }}</td>
                <td>{{ $users[$package->user_id]->name ?? 'Unknown' }}</td> <!-- Ensure booked by is displayed -->
                <td>£{{ $package->price }}</td>
            </tr>
            @php
                $totalPrice += $package->price; // Add package price to total price
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">Total</th>
            <th>£{{ $totalPrice }}</th>
        </tr>
    </tfoot>
</table>
