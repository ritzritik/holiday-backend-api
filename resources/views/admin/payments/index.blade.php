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
                <span id="payment-mode-label">
                    @if (request('payment_mode') == 4)
                        We are currently <strong>Not Accepting Payments</strong>.
                    @elseif(request('payment_mode'))
                        We are accepting payments through <span style="color: #007bff; font-weight: bold;">{{ $paymentOptions[request('payment_mode')] }}</span>.
                    @endif
                </span>
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
                                                '{{ $payment->cardDetails->card_number ?? '' }}',
                                                '{{ $payment->cardDetails->card_holder_name ?? '' }}',
                                                '{{ $payment->cardDetails->expiry_date ?? '' }}',
                                                '{{ $payment->cardDetails->cvv ?? '' }}'
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
                        <form id="approve-form" method="POST">
                            @csrf
                            <input type="hidden" name="payment_id" id="modal-payment-id">
                            <input type="hidden" name="amount" id="modal-amount">

                            <div class="mb-3">
                                <label>Existing Card Details</label>
                                <p><strong>Card Number:</strong> <span id="existing-card-number"></span></p>
                                <p><strong>Card Holder Name:</strong> <span id="existing-card-holder-name"></span></p>
                                <p><strong>Expiry Date:</strong> <span id="existing-expiry-date"></span></p>
                                <p><strong>CVV:</strong> <span id="existing-cvv"></span></p>
                            </div>

                            <div id="card-element"><!-- Stripe Element will be inserted here --></div>
                            <button type="submit" class="btn btn-primary">Confirm Approval</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios@0.27.2/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
            const stripe = Stripe(
                'pk_test_51Q4VHt02ST7uJQE3YvQ59k8KcYnljkyzBq3dBY69Ot915PfAhzWywdRiYg8hYJzhjd3XzZtzVpmOMvhDkKAV1BJ600VYESqJJr'
            ); // Use your actual public key
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element'); // Mount the card element

            function openApproveModal(paymentId, amount, cardNumber, cardHolderName, expiryDate, cvv) {
                document.getElementById('modal-payment-id').value = paymentId;
                document.getElementById('modal-amount').value = amount;

                // Set existing card details
                document.getElementById('existing-card-number').textContent = cardNumber;
                document.getElementById('existing-card-holder-name').textContent = cardHolderName;
                document.getElementById('existing-expiry-date').textContent = expiryDate;
                document.getElementById('existing-cvv').textContent = cvv;

                var myModal = new bootstrap.Modal(document.getElementById('approveModal'));
                myModal.show();
            }

            function showToast(title, message, type) {
                if (type === 'success') {
                    toastr.success(message, title);
                } else {
                    toastr.error(message, title);
                }
            }

            document.getElementById('approve-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                stripe.createToken(cardElement).then(function(result) {
                    if (result.error) {
                        showToast('Error', result.error.message, 'error'); // Show error in case of failure
                    } else {
                        // Append the token to the form and submit it
                        const formData = new FormData(event.target);
                        formData.append('payment_method_id', result.token.id); // Include the token
                        formData.append('_token', '{{ csrf_token() }}'); // Include CSRF token

                        axios.post('{{ route('admin.payments.approve') }}', formData)
                            .then(response => {
                                if (response.data.success) {
                                    setTimeout(() => {
                                        showToast('Success', 'Payment approved successfully',
                                            'success');
                                    }, 1000);
                                    setTimeout(() => {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    showToast('Error', 'Payment failed: ' + response.data.error, 'error');
                                }
                            })
                            .catch(error => {
                                showToast('Error', 'Error: ' + (error.response ? error.response.data.error :
                                    error.message), 'error');
                            });
                    }
                });
            });
        </script>
    </div>
@endsection
