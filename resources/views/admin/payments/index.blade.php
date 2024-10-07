@extends('layouts.admin.master')

@section('title', 'Payment Status')

@section('content')
    <div class="container">
        <h1 class="h3 mb-4 text-gray-800">Payment Status</h1>

        <form method="GET" action="{{ route('admin.payments-details') }}">
            <div class="form-group">
                <label>Payment Mode:</label>
                <div id="payment-gateway-section">
                    @foreach ($paymentOptions as $key => $value)
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="payment_mode" id="gateway_{{ $key }}"
                                value="{{ $key }}" onchange="this.form.submit()"
                                @if (request('payment_mode') == $key || ($loop->first && !request('payment_mode'))) checked @endif>
                            <label class="form-check-label" for="gateway_{{ $key }}">{{ $value }}</label>
                        </div>
                    @endforeach
                </div>
                <span id="payment-mode-label">Not Accepting</span>
            </div>

            <div id="payment-section">
                <h4>Pending Payments</h4>
                @if ($pendingPayments->isNotEmpty())
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>User Name</th>
                                <th>Card ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingPayments as $payment)
                                <tr>
                                    <td>{{ $payment->user_id }}</td>
                                    <td>{{ $payment->user->name }}</td>
                                    <td>{{ $payment->card_id }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm"
                                            onclick="openApproveModal(
                                                {{ $payment->id }},
                                                '{{ $payment->amount }}',
                                                '{{ $payment->cardDetails->card_number ?? 'N/A' }}',
                                                '{{ $payment->cardDetails->card_holder_name ?? 'N/A' }}',
                                                '{{ $payment->cardDetails->expiry_date ?? 'N/A' }}',
                                                '{{ $payment->cardDetails->billing_address ?? 'N/A' }}',
                                                '{{ $payment->cardDetails->cvv ?? 'N/A' }}',
                                                // '{{ $payment->payment_method_id }}'
                                            )">Approve</button>
                                        <button type="submit" class="btn btn-danger btn-sm" name="reject_payment_id"
                                            value="{{ $payment->id }}">Reject</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No pending payments.</p>
                @endif
            </div>

            <button type="submit" class="btn btn-success" id="accept-button" style="display:none;">Accepting
                Payments</button>
        </form>

        <!-- Modal for Approving Payment -->
        <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel">Approve Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="approve-form" method="POST" action="{{ route('admin.payments.approve') }}">
                            @csrf
                            <input type="hidden" name="payment_id" id="modal-payment-id">
                            <input type="hidden" name="amount" id="modal-amount">

                            <div class="mb-3">
                                <label for="card_number" class="form-label">Card Number</label>
                                <input type="text" class="form-control" id="modal-card_number" name="card_number"
                                    required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="card_holder_name" class="form-label">Card Holder Name</label>
                                <input type="text" class="form-control" id="modal-card_holder_name"
                                    name="card_holder_name" required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="expiry_date" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" id="modal-expiry_date" name="expiry_date"
                                    required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="billing_address" class="form-label">Billing Address</label>
                                <textarea class="form-control" id="modal-billing_address" name="billing_address" required readonly></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-control" id="modal-cvv" name="cvv" required readonly>
                            </div>
                            <button type="submit" class="btn btn-primary">Confirm Approval</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function openApproveModal(paymentId, amount, cardNumber, cardHolderName, expiryDate, billingAddress, cvv) {
                document.getElementById('modal-payment-id').value = paymentId;
                document.getElementById('modal-amount').value = amount;
                document.getElementById('modal-card_number').value = cardNumber;
                document.getElementById('modal-card_holder_name').value = cardHolderName;
                document.getElementById('modal-expiry_date').value = expiryDate;
                document.getElementById('modal-billing_address').value = billingAddress;
                document.getElementById('modal-cvv').value = cvv;

                var myModal = new bootstrap.Modal(document.getElementById('approveModal'));
                myModal.show();

                document.getElementById('approve-form').addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    const formData = new FormData(this); // Get form data
                    formData.append('_token', '{{ csrf_token() }}'); // Ensure the CSRF token is included

                    console.log('Submitting form with data:', Array.from(formData.entries())); // Log form data

                    axios.post('{{ route('admin.payments.approve') }}', formData)
                        .then(response => {
                            console.log('Response from server:', response.data); // Log server response
                            if (response.data.success) {
                                alert('Payment approved successfully');
                                location.reload(); // Refresh or update the UI
                            } else {
                                alert('Payment failed: ' + response.data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error during payment approval:', error); // Log any errors
                            alert('Error: ' + (error.response ? error.response.data.error : error.message));
                        });
                });
            }
        </script>

        <!-- Include Bootstrap CSS and JS -->
        <script src="https://cdn.jsdelivr.net/npm/axios@0.27.2/dist/axios.min.js"></script>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </div>
@endsection
