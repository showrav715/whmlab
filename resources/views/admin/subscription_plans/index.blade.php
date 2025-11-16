@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Plan Name')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Billing Cycle')</th>
                                <th>@lang('Active Subscriptions')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $plan->name }}</strong>
                                            @if($plan->description)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($plan->price, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge--{{ $plan->billing_cycle === 'monthly' ? 'primary' : 'success' }}">
                                            {{ ucfirst($plan->billing_cycle) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $plan->activeTenantSubscriptions->count() }}</span> @lang('Active')
                                    </td>
                                    <td>
                                        @if($plan->is_active)
                                            <span class="badge badge--success">@lang('Active')</span>
                                        @else
                                            <span class="badge badge--warning">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            <a href="{{ route('admin.subscription.plans.edit', $plan->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </a>
                                            
                                            <button type="button" class="btn btn-sm btn-outline--{{ $plan->is_active ? 'warning' : 'success' }} confirmationBtn"
                                                    data-action="{{ route('admin.subscription.plans.status', $plan->id) }}"
                                                    data-question="@lang('Are you sure to change status of this plan?')">
                                                <i class="la la-{{ $plan->is_active ? 'ban' : 'check' }}"></i> 
                                                {{ $plan->is_active ? __('Disable') : __('Enable') }}
                                            </button>
                                            
                                            @if($plan->activeTenantSubscriptions->count() === 0)
                                                <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-action="{{ route('admin.subscription.plans.destroy', $plan->id) }}"
                                                        data-question="@lang('Are you sure to delete this plan?')">
                                                    <i class="la la-trash"></i> @lang('Delete')
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="6">{{ __('No subscription plans found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal --}}
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <input type="hidden" name="_method" value="">
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.subscription.plans.create') }}" class="btn btn-sm btn--primary">
        <i class="las la-plus"></i>@lang('Add New Plan')
    </a>
@endpush

@push('script')
<script>
    (function ($) {
        'use strict';
        
        $('.confirmationBtn').on('click', function () {
            var modal = $('#confirmationModal');
            var data = $(this).data();
            var action = data.action;
            var question = data.question;
            
            modal.find('.question').text(question);
            modal.find('form').attr('action', action);
            
            // Set method based on action
            if (action.includes('destroy')) {
                modal.find('input[name="_method"]').val('DELETE');
            } else {
                modal.find('input[name="_method"]').val('POST');
            }
            
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush