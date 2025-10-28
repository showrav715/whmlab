@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Symbol')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Default')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currencies as $currency)
                                    <tr>
                                        <td>{{ $currencies->firstItem() + $loop->index }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $currency->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $currency->code }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $currency->symbol }}</span>
                                        </td>
                                        <td>
                                            @if($currency->is_default)
                                                <span class="badge badge--success">@lang('Base Currency')</span>
                                            @else
                                                <span class="fw-bold">1 {{ gs('cur_text') }} = {{ showAmount($currency->rate) }} {{ $currency->code }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($currency->is_default)
                                                <span class="badge badge--success">@lang('Yes')</span>
                                            @else
                                                <button type="button" class="btn btn--sm btn--outline-primary set-default-btn" 
                                                        data-id="{{ $currency->id }}" 
                                                        data-currency="{{ $currency->name }}"
                                                        @if(!$currency->status) disabled @endif>
                                                    @lang('Set Default')
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                echo $currency->status ? '<span class="badge badge--success">'.trans('Active').'</span>' : '<span class="badge badge--warning">'.trans('Disabled').'</span>';
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.currency.edit', $currency->id) }}" 
                                                   class="btn btn-sm btn-outline--primary">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                                
                                                @if($currency->status)
                                                    @if(!$currency->is_default)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline--danger confirmationBtn" 
                                                                data-action="{{ route('admin.currency.status', $currency->id) }}" 
                                                                data-question="@lang('Are you sure to disable this currency?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                        </button>
                                                    @endif
                                                @else
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline--success confirmationBtn" 
                                                            data-action="{{ route('admin.currency.status', $currency->id) }}" 
                                                            data-question="@lang('Are you sure to enable this currency?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @endif

                                                @if(!$currency->is_default)
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline--danger confirmationBtn" 
                                                            data-action="{{ route('admin.currency.destroy', $currency->id) }}" 
                                                            data-question="@lang('Are you sure to delete this currency?')">
                                                        <i class="la la-trash"></i> @lang('Delete')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($currencies->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($currencies) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- SET DEFAULT MODAL --}}
    <div id="setDefaultModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Set Default Currency')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to set') <span class="fw-bold currency-name"></span> @lang('as the default currency?')</p>
                        <p class="text--warning">@lang('This will update the site\'s base currency and all prices will be calculated relative to this currency.')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-end">
        <a href="{{ route('admin.currency.create') }}" class="btn btn-sm btn--primary">
            <i class="las la-plus"></i>@lang('Add New')
        </a>
        <a href="{{ route('admin.currency.update.rates') }}" class="btn btn-sm btn--info ms-2">
            <i class="las la-sync-alt"></i>@lang('Update Rates')
        </a>
    </div>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            
            $('.set-default-btn').on('click', function() {
                var modal = $('#setDefaultModal');
                var id = $(this).data('id');
                var currency = $(this).data('currency');
                
                modal.find('.currency-name').text(currency);
                modal.find('form').attr('action', '{{ route("admin.currency.set.default", "") }}/' + id);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush