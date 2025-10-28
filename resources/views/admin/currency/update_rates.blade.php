@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.currency.store.rates') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="alert alert--info">
                            <div class="alert__icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="alert__message">
                                @lang('Update exchange rates for all currencies relative to the base currency') (<strong>{{ gs('cur_text') }}</strong>).
                                <br>@lang('Base currency rate is always 1 and cannot be changed.')
                            </p>
                        </div>

                        @if($currencies->count() > 0)
                            <div class="row">
                                @foreach($currencies as $currency)
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="card-title mb-0">{{ $currency->name }}</h6>
                                                    <span class="badge badge--primary">{{ $currency->code }}</span>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <label class="form-label">@lang('Exchange Rate')</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">1 {{ gs('cur_text') }} =</span>
                                                        <input type="number" 
                                                               class="form-control" 
                                                               name="rates[{{ $currency->id }}]" 
                                                               value="{{ $currency->rate }}" 
                                                               step="0.00000001" 
                                                               min="0.00000001" 
                                                               required>
                                                        <span class="input-group-text">{{ $currency->code }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-thumb">
                                    <img src="{{ asset('assets/images/empty_list.png') }}" alt="@lang('empty')">
                                    <p class="fs--14px mt-3">@lang('No currencies found to update rates')</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($currencies->count() > 0)
                        <div class="card-footer">
                            <button type="submit" class="btn btn--primary w-100 h-45">
                                <i class="las la-sync-alt"></i> @lang('Update Exchange Rates')
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Exchange Rate API Integration (Optional Enhancement) --}}
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Auto Update Rates')</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert--warning">
                        <div class="alert__icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <p class="alert__message">
                            @lang('Coming Soon: Automatic exchange rate updates using external APIs')
                            <br><small>@lang('For now, please update rates manually or implement your preferred exchange rate provider')</small>
                        </p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>@lang('Supported Providers (Future)')</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text--success"></i> Open Exchange Rates</li>
                                <li><i class="fas fa-check text--success"></i> Fixer.io</li>
                                <li><i class="fas fa-check text--success"></i> CurrencyAPI</li>
                                <li><i class="fas fa-check text--success"></i> ExchangeRate-API</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>@lang('Features')</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-clock text--info"></i> Automatic daily updates</li>
                                <li><i class="fas fa-history text--info"></i> Rate history tracking</li>
                                <li><i class="fas fa-bell text--info"></i> Rate change notifications</li>
                                <li><i class="fas fa-shield-alt text--info"></i> Fallback rates</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.currency.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            
            // Add some helpful functionality for rate updates
            $('.card-body input[type="number"]').on('focus', function() {
                $(this).select();
            });
            
            // Optional: Add validation for reasonable exchange rates
            $('.card-body input[type="number"]').on('blur', function() {
                var rate = parseFloat($(this).val());
                var currencyCode = $(this).closest('.input-group').find('.input-group-text:last').text();
                
                if (rate <= 0) {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">Rate must be greater than 0</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
        })(jQuery);
    </script>
@endpush