@extends($activeTemplate . 'layouts.master_side_bar')

@section('content')
    <div class="col-lg-9">

        <div class="notice"></div>

        @if ($user->kv == 0 || $user->kv == 2)
            @php
                $kyc = @getContent('kyc.content', true);
            @endphp

            <div class="row">
                <div class="col-md-12">
                    <div class="card card custom--card style-two mb-4 mb-4 bg--navajowhite">
                        <div class="card-body">
                            @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
                                <div class="justify-content-between d-flex flex-wrap gap-4">
                                    <div class="d-flex justify-content-between flex-wrap align-items-center gap-2">
                                        <h6>@lang('KYC Documents Rejected')</h6>
                                        <button class="btn btn-outline-secondary btn--sm" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">
                                            @lang('Show Reason')
                                        </button>
                                    </div>
                                    <a href="{{ route('user.kyc.form') }}" class="text-bold text--primary">@lang('Click Here to Re-submit Documents')</a>
                                </div>
                                <hr>
                                <p>{{ __(@$kyc->data_values->kyc_reject) }}</p>
                            @elseif($user->kv == Status::KYC_UNVERIFIED)
                                <div class="justify-content-between d-flex flex-wrap">
                                    <h6>@lang('KYC Verification Required')</h6>
                                    <a href="{{ route('user.kyc.form') }}" class="text-bold text--primary">@lang('Click Here to Submit Documents')</a>
                                </div>
                                <hr>
                                <p>{{ __(@$kyc->data_values->kyc_required) }}</p>
                            @elseif($user->kv == Status::KYC_PENDING)
                                <div class="justify-content-between d-flex flex-wrap">
                                    <h6>@lang('KYC Verification Pending')</h6>
                                    <a href="{{ route('user.kyc.data') }}" class="text-bold text--primary">@lang('See KYC Data')</a>
                                </div>
                                <hr>
                                <p>{{ __(@$kyc->data_values->kyc_pending) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row user-dashboard">
            <div class="col-xl-4 col-md-6 col-sm-6">
                <div class="custom--card custom-radius-10 border-start border-0 border-3 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text--secondary">@lang('Balance')</p>
                                <h4 class="my-1">{{ showAmount($user->balance) }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded--circle bg-gradient-blooker text--white ms--auto">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <a href="{{ route('user.transactions') }}" class="has-anchor"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-sm-6">
                <div class="custom--card custom-radius-10 border-start border-0 border-3 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text--secondary">@lang('Deposit')</p>
                                <h4 class="my-1">{{ @$user->deposits->count() }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded--circle bg-gradient-blooker text--white ms--auto">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <a href="{{ route('user.deposit.history') }}" class="has-anchor"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-sm-6">
                <div class="custom--card custom-radius-10 border-start border-0 border-3 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text--secondary">@lang('Services')</p>
                                <h4 class="my-1">{{ $totalService }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded--circle bg-gradient-scooter text--white ms--auto">
                                <i class="fa fa-box"></i>
                            </div>
                            <a href="{{ route('user.service.list') }}" class="has-anchor"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-sm-6">
                <div class="custom--card custom-radius-10 border-start border-0 border-3 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text--secondary">@lang('Domains')</p>
                                <h4 class="my-1">{{ $totalDomain }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded--circle bg-gradient-bloody text--white ms--auto">
                                <i class="fas fa-globe"></i>
                            </div>
                            <a href="{{ route('user.domain.list') }}" class="has-anchor"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-sm-6">
                <div class="custom--card custom-radius-10 border-start border-0 border-3 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text--secondary">@lang('Tickets')

                                </p>
                                <h4 class="my-1">{{ $totalTicket }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded--circle bg-gradient-ohhappiness text--white ms--auto">
                                <i class="fa fa-comments"></i>
                            </div>
                            <a href="{{ route('ticket.index') }}" class="has-anchor"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-sm-6">
                <div class="custom--card custom-radius-10 border-start border-0 border-3 border-left-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text--secondary">@lang('Invoices')</p>
                                <h4 class="my-1">{{ $totalInvoice }}</h4>
                            </div>
                            <div class="widgets-icons-2 rounded--circle bg-gradient-blooker text--white ms--auto">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <a href="{{ route('user.invoice.list') }}" class="has-anchor"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark">
                    <div class="card-body">
                        <div class="justify-content-between d-flex mb-3 flex-wrap">
                            <div> <i class="fas fa-calculator"></i> @lang('Overdue Invoices') </div>
                            <a class="btn btn--xs btn--base" href="{{ route('user.invoice.list') }}">
                                <i class="fas fa-list"></i> @lang('View All')
                            </a>
                        </div>
                        @lang('You have') {{ $totalOverDueInvoice->total }}
                        @lang('overdue invoice(s) with a total balance due of')
                        {{ showAmount($totalOverDueInvoice->totalDue) }}.
                        @lang('Pay them now to avoid any interruptions in service').
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark h-100">
                    <div class="card-body">
                        <div class="justify-content-between d-flex mb-3 flex-wrap">
                            <div> <i class="fas fa-cube"></i> @lang('Products/Services') </div>
                            <a class="btn btn--xs btn--base" href="{{ route('user.service.list') }}"> <i class="fas fa-list"></i> @lang('View All')</a>
                        </div>
                        @lang('It appears you do not have any products/services with us yet').
                        <a href="{{ route('service.category') }}?all">@lang('Place an order to get started')</a>.
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark h-100">
                    <div class="card-body">
                        <div class="justify-content-between d-flex mb-3 flex-wrap">
                            <div> <i class="fas fa-comments"></i> @lang('Support Tickets') </div>
                            <a class="btn btn--xs btn--base" href="{{ route('ticket.index') }}"> <i class="fas fa-list"></i> @lang('View All')</a>
                        </div>
                        @lang('No Recent Tickets Found. If you need any help'), <a href="{{ route('ticket.open') }}">@lang('please open a ticket')</a>.
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="card custom-border-top-dark h-100">
                    <div class="card-body">
                        <div> <i class="fas fa-globe"></i> @lang('Register New Domain') </div>
                        <form action="" class="form mt-4">
                            <div class="form-group position-relative mb-0">
                                <div class="domain-search-icon"><i class="fas fa-search"></i></div>
                                <input class="form-control form--control h-45" type="text" name="domain" placeholder="@lang('Domain name or keyword')" required>
                                <div class="domain-search-icon-reset">
                                    <button class="btn btn--base btn--sm" type="submit">@lang('Search')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $user->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.form').on('submit', function(e) {
                e.preventDefault();
                var domain = $(this).find('input[name=domain]').val();
                window.location.href = "{{ route('register.domain') }}?domain=" + domain;
            })
        })(jQuery);
    </script>
@endpush
