@extends('admin.layouts.app')
@section('panel')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card text-center">
                <div class="card-body py-5">
                    <div class="success-icon mb-4">
                        <i class="las la-check-circle text-success" style="font-size: 5rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">@lang('Payment Successful!')</h2>
                    <p class="text-muted mb-4">
                        @lang('Thank you! Your subscription has been renewed successfully. Your new plan is now active and you can enjoy all the features.')
                    </p>

                    <div class="success-details">
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <h6 class="mb-2">@lang('What happens next?')</h6>
                                    <ul class="list-unstyled mb-0 text-start">
                                        <li><i class="las la-check text-success me-2"></i>@lang('Your subscription is now active')</li>
                                        <li><i class="las la-check text-success me-2"></i>@lang('All features are immediately available')</li>
                                        <li><i class="las la-check text-success me-2"></i>@lang('You will receive a confirmation email shortly')</li>
                                        <li><i class="las la-check text-success me-2"></i>@lang('Invoice details have been saved to your account')</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn--primary me-3">
                            <i class="las la-home"></i>
                            @lang('Go to Dashboard')
                        </a>
                        <a href="{{ route('admin.subscription.index') }}" class="btn btn--secondary">
                            <i class="las la-eye"></i>
                            @lang('View Subscription')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .success-icon {
            animation: successPulse 2s infinite;
        }

        @keyframes successPulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        .action-buttons {
            margin-top: 2rem;
        }

        .success-details .alert {
            text-align: left;
        }
    </style>
@endsection