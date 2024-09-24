<table class="table table-bordered">
    <thead>
        <tr>
            <th>Holiday Code</th>
            <th>Holiday Name</th>
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
        @foreach ($skiholiday as $holiday)
            <tr>
                <td>{{ $holiday->name }}</td>
                <td>{{ $holiday->price }}</td>
                <td>{{ $holiday->duration }} days</td>
                <td>{{ $holiday->created_at->format('d/m/Y') }}</td>
                <td>{{ $users[$holiday->created_by]->name ?? 'Unknown' }}</td>
                <td>£{{ $holoiday->price }}</td>
            </tr>
            @php
                $totalPrice += $holiday->price;
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
