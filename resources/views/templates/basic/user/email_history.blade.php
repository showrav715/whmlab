@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <table class="table table--responsive--md">
            <thead>
                <tr>
                    <th>@lang('Sent')</th>
                    <th>@lang('Subject')</th>
                    <th>@lang('Action')</th>
                </tr> 
            </thead>
            <tbody>
                @forelse($emails as $email) 
                    <tr>
                        <td>
                            <div>
                                {{ showDateTime($email->created_at) }}
                                <br>
                                {{ $email->created_at->diffForHumans() }}
                            </div>
                        </td>
                        <td>
                            {{ __($email->subject) }}
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="badge badge--icon badge--fill-base modalBtn" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('View')"
                                data-message="{{ route('user.email.details', $email->id) }}" 
                            >
                                <i class="fas fa-desktop"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <x-empty-message table={{ true }} />
                @endforelse
            </tbody>
        </table>
        @if($emails->hasPages())
            <div class="mt-5">
                {{ paginateLinks($emails) }}
            </div>
        @endif
    </div>
</div> 

<div id="notifyDetailModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">@lang('Details')</h6>
                <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </span>
            </div>
            <div class="modal-body">
                <div class="detail"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.modalBtn').on('click', function () {
                var modal = $('#notifyDetailModal');
 
                var message = $(this).data('message');
                var details = `<iframe src="${message}" height="500" width="100%" title="@lang('Details')"></iframe>`
                
                $('.detail').html(details)
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
