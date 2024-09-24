<table class="table table-bordered">
    <thead>
        <tr>
            <th>Hotel Code</th>
            <th>Hotel Name</th>
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
        @foreach ($hotels as $package)
            <tr>
                <td>{{ $hotel->name }}</td>
                <td>{{ $hotel->price }}</td>
                <td>{{ $hotel->duration }} days</td>
                <td>{{ $hotel->created_at->format('d/m/Y') }}</td>
                <td>{{ $users[$hotel->created_by]->name ?? 'Unknown' }}</td>
                <td>£{{ $hotel->price }}</td>
            </tr>
            @php
                $totalPrice += $hotel->price;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total</th>
            <th colspan="2"></th>
            <th>£{{ $totalPrice }}</th>
        </tr>
    </tfoot>
</table>
