@extends('layouts.admin.master')

@section('title', 'Payment Status')

@section('content')
<div class="container">
    <h1 class="h3 mb-4 text-gray-800">Payment Status</h1>

    <form method="POST" action="{{ route('admin.payments.accept') }}">
        @csrf
        <div class="form-group">
            <label>Payment Mode:</label>
            <div id="payment-gateway-section">
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="payment_gateway" id="stripe" value="stripe" onchange="updatePaymentMode()">
                    <label class="form-check-label" for="stripe">Stripe</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="payment_gateway" id="ecom_pay" value="ecom_pay" onchange="updatePaymentMode()">
                    <label class="form-check-label" for="ecom_pay">Ecom Pay</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="payment_gateway" id="sage_pay" value="sage_pay" onchange="updatePaymentMode()">
                    <label class="form-check-label" for="sage_pay">Sage Pay</label>
                </div>
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
                                    <button type="button" class="btn btn-success btn-sm" onclick="openApproveModal(
                                        {{ $payment->id }},
                                        '{{ $payment->cardDetails->card_number ?? 'N/A' }}',
                                        '{{ $payment->cardDetails->card_holder_name ?? 'N/A' }}',
                                        '{{ $payment->cardDetails->expiry_month ?? 'N/A' }}',
                                        '{{ $payment->cardDetails->expiry_year ?? 'N/A' }}',
                                        '{{ $payment->cardDetails->billing_address ?? 'N/A' }}',
                                        '{{ $payment->cardDetails->cvv ?? 'N/A' }}'
                                    )">Approve</button>
                                    <button type="submit" class="btn btn-danger btn-sm" name="reject_payment_id" value="{{ $payment->id }}">Reject</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No pending payments.</p>
            @endif
        </div>

        <button type="submit" class="btn btn-success" id="accept-button" style="display:none;">Accepting Payments</button>
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
                        <div class="mb-3">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="modal-card_number" name="card_number" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="card_holder_name" class="form-label">Card Holder Name</label>
                            <input type="text" class="form-control" id="modal-card_holder_name" name="card_holder_name" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="expiry_month" class="form-label">Expiry Month</label>
                            <input type="text" class="form-control" id="modal-expiry_month" name="expiry_month" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="expiry_year" class="form-label">Expiry Year</label>
                            <input type="text" class="form-control" id="modal-expiry_year" name="expiry_year" required readonly>
                        </div>
                        <div class="mb-3">
                            <label for="billing_address" class="form-label">Billing Address</label>
                            <textarea class="form-control" id="modal-billing_address" name="billing_address" required readonly></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="password" class="form-control" id="modal-cvv" name="cvv" required readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Confirm Approval</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updatePaymentMode() {
            var paymentModeLabel = document.getElementById('payment-mode-label');
            var acceptButton = document.getElementById('accept-button');
            var selectedGateway = document.querySelector('input[name="payment_gateway"]:checked');

            if (selectedGateway) {
                paymentModeLabel.textContent = 'Accepting: ' + selectedGateway.nextElementSibling.textContent;
                acceptButton.style.display = 'inline';
            } else {
                paymentModeLabel.textContent = 'Not Accepting';
                acceptButton.style.display = 'none';
            }
        }

        // Initialize radio button state on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePaymentMode();
        });

        function openApproveModal(paymentId, cardNumber, cardHolderName, expiryMonth, expiryYear, billingAddress, cvv) {
            document.getElementById('modal-payment-id').value = paymentId;
            document.getElementById('modal-card_number').value = cardNumber;
            document.getElementById('modal-card_holder_name').value = cardHolderName;
            document.getElementById('modal-expiry_month').value = expiryMonth;
            document.getElementById('modal-expiry_year').value = expiryYear;
            document.getElementById('modal-billing_address').value = billingAddress;
            document.getElementById('modal-cvv').value = cvv;
            var myModal = new bootstrap.Modal(document.getElementById('approveModal'));
            myModal.show();
        }
    </script>

    <!-- Include Bootstrap CSS and JS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</div>
@endsection
