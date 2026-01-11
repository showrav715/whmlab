@extends('admin.layouts.app')
@section('panel')
    <!-- Hidden CSRF Token for JavaScript -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token">
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="las la-credit-card me-2"></i>
                        @lang('Subscription Renewal')
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Plan Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="plan-details">
                                <h6 class="text-primary">@lang('Selected Plan')</h6>
                                <h4>{{ $subscriptionPlan->name }}</h4>
                                <p class="text-muted">{{ $subscriptionPlan->description }}</p>
                                
                                @if($subscriptionPlan->features && is_array($subscriptionPlan->features))
                                    <h6 class="mt-3">@lang('Features Included'):</h6>
                                    <ul class="list-unstyled">
                                        @foreach($subscriptionPlan->features as $feature)
                                            <li>
                                                <i class="las la-check text-success"></i>
                                                {{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="payment-summary">
                                <h6 class="text-primary">@lang('Payment Summary')</h6>
                                <div class="summary-item">
                                    <span>@lang('Plan'):</span>
                                    <span>{{ $subscriptionPlan->name }}</span>
                                </div>
                                <div class="summary-item">
                                    <span>@lang('Duration'):</span>
                                    <span>{{ ucfirst($subscriptionPlan->billing_cycle) }}</span>
                                </div>
                                <div class="summary-item">
                                    <span>@lang('Amount'):</span>
                                    <span>{{ gs()->cur_sym }}{{ showAmount($subscriptionPlan->price) }}</span>
                                </div>
                                @if($currentSubscription)
                                <div class="summary-item">
                                    <span>@lang('Current Expires'):</span>
                                    <span>{{ $currentSubscription->expires_at->format('M j, Y') }}</span>
                                </div>
                                <div class="summary-item">
                                    <span>@lang('New Expires'):</span>
                                    <span>{{ now()->addMonths($subscriptionPlan->billing_cycle === 'monthly' ? 1 : 12)->format('M j, Y') }}</span>
                                </div>
                                @endif
                                <hr>
                                <div class="summary-item total">
                                    <strong>
                                        <span>@lang('Total Amount'):</span>
                                        <span class="text-primary">{{ gs()->cur_sym }}{{ showAmount($subscriptionPlan->price) }}</span>
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="payment-methods mb-4">
                        <h6 class="text-primary">@lang('Select Payment Method')</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="payment-method-card active" data-method="razorpay">
                                    <div class="payment-method-icon">
                                        <img src="{{ asset('assets/images/gateway/razorpay.png') }}" alt="Razorpay" style="height: 40px;">
                                    </div>
                                    <div class="payment-method-details">
                                        <h6>@lang('Razorpay')</h6>
                                        <small class="text-muted">@lang('Pay securely with cards, UPI, net banking')</small>
                                    </div>
                                    <div class="payment-method-check">
                                        <i class="las la-check-circle text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form id="payment-form" class="payment-form">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $subscriptionPlan->id }}">
                        <input type="hidden" name="payment_method" value="razorpay">
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="terms">@lang('Terms and Conditions')</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            @lang('I agree to the') 
                                            <a href="#" target="_blank">@lang('Terms and Conditions')</a>
                                            @lang('and') 
                                            <a href="#" target="_blank">@lang('Privacy Policy')</a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="{{ route('admin.subscription.index') }}" class="btn btn--secondary">
                                <i class="las la-arrow-left"></i>
                                @lang('Back to Plans')
                            </a>
                            <button type="submit" class="btn btn--primary" id="pay-now-btn">
                                <i class="las la-lock"></i>
                                @lang('Pay Now') - {{ gs()->cur_sym }}{{ showAmount($subscriptionPlan->price) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .plan-details h4 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-item.total {
            border-bottom: none;
            font-size: 1.1rem;
            margin-top: 10px;
            padding-top: 15px;
        }

        .payment-method-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .payment-method-card:hover {
            border-color: #007bff;
        }

        .payment-method-card.active {
            border-color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
        }

        .payment-method-icon {
            margin-right: 15px;
        }

        .payment-method-details {
            flex-grow: 1;
        }

        .payment-method-details h6 {
            margin-bottom: 5px;
        }

        .payment-method-check {
            font-size: 1.5rem;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .payment-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }

        #pay-now-btn {
            min-width: 200px;
        }
    </style>

    <!-- Razorpay Checkout Script -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentForm = document.getElementById('payment-form');
            const payNowBtn = document.getElementById('pay-now-btn');

            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check terms and conditions
                if (!document.getElementById('terms').checked) {
                    alert('Please accept the terms and conditions.');
                    return;
                }

                // Disable button and show loading
                payNowBtn.disabled = true;
                payNowBtn.innerHTML = '<i class="las la-spinner la-spin"></i> @lang("Processing...")';

                // Process payment
                processPayment();
            });

            function processPayment() {
                // Get CSRF token safely
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 document.querySelector('input[name="_token"]')?.value || 
                                 '{{ csrf_token() }}';
                
                fetch('{{ route("admin.subscription.process.payment") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        plan_id: {{ $subscriptionPlan->id }},
                        payment_method: 'razorpay'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Initialize Razorpay
                        const options = {
                            key: data.key,
                            amount: data.amount,
                            currency: data.currency,
                            name: data.name,
                            description: data.description,
                            order_id: data.order_id,
                            prefill: data.prefill,
                            theme: {
                                color: '#007bff'
                            },
                            handler: function(response) {
                                // Payment successful
                                handlePaymentSuccess(response);
                            },
                            modal: {
                                ondismiss: function() {
                                    // Payment cancelled
                                    resetPaymentButton();
                                }
                            }
                        };

                        const rzp = new Razorpay(options);
                        rzp.open();
                    } else {
                        alert('Payment initialization failed: ' + data.error);
                        resetPaymentButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Payment processing failed. Please try again.');
                    resetPaymentButton();
                });
            }

            function handlePaymentSuccess(response) {
                // Send payment success data to server
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.subscription.payment.success") }}';
                form.style.display = 'none';

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 document.querySelector('input[name="_token"]')?.value || 
                                 '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                // Add payment response data
                for (const key in response) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = response[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
            }

            function resetPaymentButton() {
                payNowBtn.disabled = false;
                payNowBtn.innerHTML = '<i class="las la-lock"></i> @lang("Pay Now") - {{ gs()->cur_sym }}{{ showAmount($subscriptionPlan->price) }}';
            }
        });
    </script>
@endsection