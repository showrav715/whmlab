@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.currency.update', $currency->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">@lang('Currency Name')</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $currency->name) }}" required>
                                    <small class="form-text text-muted">@lang('e.g., US Dollar, Euro, Bitcoin')</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label required">@lang('Currency Code')</label>
                                    <input type="text" class="form-control" name="code" value="{{ old('code', $currency->code) }}" maxlength="10" required>
                                    <small class="form-text text-muted">@lang('e.g., USD, EUR, BTC')</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label required">@lang('Currency Symbol')</label>
                                    <input type="text" class="form-control" name="symbol" value="{{ old('symbol', $currency->symbol) }}" maxlength="10" required>
                                    <small class="form-text text-muted">@lang('e.g., $, €, ₿')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label required">@lang('Exchange Rate')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">1 {{ gs('cur_text') }} =</span>
                                        <input type="number" class="form-control" name="rate" value="{{ old('rate', $currency->rate) }}" step="0.00000001" min="0.00000001" required @if($currency->is_default) readonly @endif>
                                        <span class="input-group-text currency-code">{{ $currency->code }}</span>
                                    </div>
                                    @if($currency->is_default)
                                        <small class="form-text text-warning">@lang('Rate is always 1 for the default currency')</small>
                                    @else
                                        <small class="form-text text-muted">@lang('Exchange rate relative to') {{ gs('cur_text') }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">@lang('Sort Order')</label>
                                    <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $currency->sort_order) }}" min="0">
                                    <small class="form-text text-muted">@lang('Lower numbers appear first')</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">@lang('Options')</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="status" id="status" {{ old('status', $currency->status) ? 'checked' : '' }} @if($currency->is_default) disabled @endif>
                                            <label class="form-check-label" for="status">
                                                @lang('Active')
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default" {{ old('is_default', $currency->is_default) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_default">
                                                @lang('Set as Default')
                                            </label>
                                        </div>
                                    </div>
                                    @if($currency->is_default)
                                        <small class="form-text text-warning">@lang('Default currency is always active')</small>
                                    @else
                                        <small class="form-text text-muted">@lang('Setting as default will replace the current base currency')</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update')</button>
                    </div>
                </form>
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
            
            $('input[name="code"]').on('input', function() {
                var code = $(this).val().toUpperCase();
                $(this).val(code);
                $('.currency-code').text(code || '---');
            });
            
            // Handle default currency logic
            $('input[name="is_default"]').on('change', function() {
                var isDefault = $(this).is(':checked');
                var rateInput = $('input[name="rate"]');
                var statusInput = $('input[name="status"]');
                
                if (isDefault) {
                    rateInput.val(1).prop('readonly', true);
                    statusInput.prop('checked', true).prop('disabled', true);
                } else {
                    rateInput.prop('readonly', false);
                    statusInput.prop('disabled', false);
                }
            });
            
            // Initialize state
            var isDefault = $('input[name="is_default"]').is(':checked');
            if (isDefault) {
                $('input[name="rate"]').val(1);
            }
        })(jQuery);
    </script>
@endpush