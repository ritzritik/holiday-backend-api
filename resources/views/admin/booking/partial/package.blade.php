<table class="table table-bordered">
    <thead>
        <tr>
            <th>Package Code</th>
            <th>Package Name</th>
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
                <td>{{ $package->agent_id }}</td>
                <td>{{ $package->package_name }}</td>
                <td>{{ $package->duration }} days</td>
                <td>{{ $package->created_at->format('d/m/Y') }}</td>
                <td>{{ $users[$package->created_by]->name ?? 'Unknown' }}</td>
                <td>£{{ $package->price }}</td>
            </tr>
            @php
                $totalPrice += $package->price; // Add package price to total price
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">Total</th>
            <th>£{{ $totalPrice }}</th>
        </tr>
    </tfoot>
</table>
