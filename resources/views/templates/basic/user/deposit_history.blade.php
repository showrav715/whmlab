@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="pt-60 pb-60 bg--light section-full">
        <div class="container">

            <div class="mb-4 d-flex justify-content-end">
                <x-search-form placeholder="Search by transactions" btn="btn--base" class="form-control form--control h-45" />
            </div>

            <table class="table table--responsive--lg">
                <thead>
                    <tr>
                        <th>@lang('Gateway')</th>
                        <th>@lang('Transacted')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Conversion')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deposits as $deposit)
                        <tr>
                            <td>
                                <div>
                                    <span class="text--base">{{ __($deposit->gateway?->name) }}</span>
                                    <br>
                                    {{ $deposit->trx }}
                                </div>
                            </td>
                            <td>
                                {{ showDateTime($deposit->created_at, 'M d, Y, h:i a') }}
                            </td>
                            <td>
                                <div>
                                    {{ gs('cur_sym') }}{{ showAmount($deposit->amount) }} +
                                    <span class="text--danger" title="@lang('charge')">
                                        {{ showAmount($deposit->charge) }}
                                    </span>
                                    <br>
                                    <strong title="@lang('Amount with charge')">
                                        {{ showAmount($deposit->amount + $deposit->charge) }} {{ __(gs('cur_text')) }}
                                    </strong>
                                </div>
                            </td>
                            <td>
                                <div>
                                    1 {{ __(gs('cur_text')) }} = {{ showAmount($deposit->rate) }} {{ __($deposit->method_currency) }}
                                    <br>
                                    <strong>{{ showAmount($deposit->final_amo) }} {{ __($deposit->method_currency) }}</strong>
                                </div>
                            </td>
                            <td>
                                @php echo $deposit->statusBadge @endphp
                            </td>
                            <td>
                                @if ($deposit->method_code >= 1000)
                                    @php
                                        $details = $deposit->detail != null ? json_encode($deposit->detail) : null;
                                    @endphp
                                    <a href="javascript:void(0)" class="badge badge--icon badge--fill-base detailBtn" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('View')" @if ($deposit->method_code >= 1000) data-info="{{ $details }}" @endif @if ($deposit->status == 3) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                        <i class="fas fa-desktop"></i>
                                    </a>
                                @else
                                    <a href="javascript:void(0)" class="badge badge--icon badge--fill-base disabled" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('View')">
                                        <i class="fas fa-desktop"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-empty-message table={{ true }} />
                    @endforelse
                </tbody>
            </table>
            @if ($deposits->hasPages())
                <div class="mt-5">
                    {{ paginateLinks($deposits) }}
                </div>
            @endif
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">@lang('Details')</h6>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class='fw-bold'>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
