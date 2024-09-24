<table class="table table-bordered">
    <thead>
        <tr>
            <th>Flight Number</th>
            <th>Flight Name</th>
            <th>Booked On</th>
            <th>Booked By</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalPrice = 0;
        @endphp
        @foreach ($flights as $flight)
            <tr>
                <td>{{ $flight->flight_number }}</td>
                <td>{{ $flight->flight_name }}</td>
                <td>{{ $flight->created_at->format('d/m/Y') }}</td>
                <td>{{ $users[$flight->created_by]->name ?? 'Unknown'}}</td>
                <td>£{{ $flight->price }}</td>
            </tr>
            @php
                $totalPrice += $flight->price;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total Pending Flights</th>
            <th colspan="2"></th>
            <th>£{{ $totalPrice }}</th>
        </tr>
    </tfoot>
</table>
