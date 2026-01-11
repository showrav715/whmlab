@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body">
                <form action="{{ route('admin.subscription.plans.update', $plan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Plan Name') <span class="text--danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">@lang('Price') <span class="text--danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price', $plan->price) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">@lang('Billing Cycle') <span class="text--danger">*</span></label>
                                <select name="billing_cycle" class="form-control" required>
                                    <option value="">@lang('Select Cycle')</option>
                                    <option value="monthly" {{ old('billing_cycle', $plan->billing_cycle) === 'monthly' ? 'selected' : '' }}>@lang('Monthly')</option>
                                    <option value="yearly" {{ old('billing_cycle', $plan->billing_cycle) === 'yearly' ? 'selected' : '' }}>@lang('Yearly')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Description')</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Plan description">{{ old('description', $plan->description) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Features')</label>
                                <div id="featuresContainer">
                                    @php
                                        $features = old('features', $plan->features ?? []);
                                    @endphp
                                    @if($features && count($features) > 0)
                                        @foreach($features as $feature)
                                            <div class="input-group mb-2">
                                                <input type="text" name="features[]" class="form-control" value="{{ $feature }}" placeholder="Feature description">
                                                <button type="button" class="btn btn--danger remove-feature">
                                                    <i class="las la-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" name="features[]" class="form-control" placeholder="Feature description">
                                            <button type="button" class="btn btn--danger remove-feature">
                                                <i class="las la-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="btn btn--primary btn-sm add-feature">
                                    <i class="las la-plus"></i> @lang('Add Feature')
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Sort Order')</label>
                                <input type="number" name="sort_order" class="form-control" min="0" value="{{ old('sort_order', $plan->sort_order) }}">
                                <small class="form-text text-muted">@lang('Lower numbers appear first')</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">@lang('Status')</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">@lang('Active')</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($plan->activeTenantSubscriptions->count() > 0)
                        <div class="alert alert-warning">
                            <h6><i class="las la-exclamation-triangle"></i> @lang('Warning')</h6>
                            <p class="mb-0">@lang('This plan has') <strong>{{ $plan->activeTenantSubscriptions->count() }}</strong> @lang('active subscriptions. Changes may affect existing subscribers.')</p>
                        </div>
                    @endif

                    <div class="form-group">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Update Plan')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.subscription.plans.index') }}" class="btn btn-sm btn--dark">
        <i class="la la-undo"></i>@lang('Back')
    </a>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';
        
        // Add new feature
        $('.add-feature').on('click', function() {
            var newFeature = `
                <div class="input-group mb-2">
                    <input type="text" name="features[]" class="form-control" placeholder="Feature description">
                    <button type="button" class="btn btn--danger remove-feature">
                        <i class="las la-times"></i>
                    </button>
                </div>
            `;
            $('#featuresContainer').append(newFeature);
        });
        
        // Remove feature
        $(document).on('click', '.remove-feature', function() {
            if ($('#featuresContainer .input-group').length > 1) {
                $(this).closest('.input-group').remove();
            }
        });
        
    })(jQuery);
</script>
@endpush