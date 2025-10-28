@extends('admin.layouts.app')
@section('panel')
<div class="notify__area">
    @forelse($notifications as $notification)
        <div class="notify__item d-md-flex d-block align-items-start justify-content-between  @if ($notification->is_read == 0) unread--notification @endif">
            <div class="ps-0 w-100"> 
                <a href="{{ permit('admin.notification.read') ? route('admin.notification.read', $notification->id) : 'javascript:void(0)' }}">
                    <h6 class="title mb-2"><i class="las la-hand-pointer text--primary"></i> {{ __($notification->title) }} xx</h6>
                </a>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="date small text--primary">
                        <i class="las la-clock"></i> {{ $notification->created_at->diffForHumans() }}
                        <i class="las la-{{ $notification->is_read == 0 ? 'eye-slash' : 'eye' }} ms-1"> 
                            <span>({{ $notification->is_read == 0 ? 'unseen' : 'seen' }})</span>
                        </i>
                    </span>
                    @permit('admin.delete.automation.error')
                        <button class="btn btn-sm btn-outline--danger flex-shrink-0 mt-md-0 mt-2 delete ms-2" type="button" data-id="{{ $notification->id }}"> 
                            <i class="las la-trash"></i> @lang('Delete')
                        </button>
                    @endpermit
                </div>
            </div> 
        </div>
    @empty  
    <div class="card">
        <div class="card-body">
            <div class="empty-notification-list text-center">
                <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                <h5 class="text-muted">{{ __($emptyMessage) }}</h5>
            </div>
        </div>
    </div>
    @endforelse
    <div class="mt-3">
        {{ paginateLinks($notifications) }}
    </div>
</div> 

<x-confirmation-modal />

<div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form>
                <div class="modal-body">
                    <p class="question">
                        @lang('Are you sure to delete this records')?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary submitBtn">@lang('Yes')</button>
                </div> 
            </form>
        </div>
    </div>
</div>
@endsection

@if($notifications->count())
    @push('breadcrumb-plugins')
        <div class="d-flex justify-content-end flex-wrap gap-2">
            @permit('admin.delete.automation.errors')
                <button class="btn btn-sm btn-outline--danger confirmationBtn"
                    data-action="{{ route('admin.delete.automation.errors') }}"
                    data-question="@lang('Are you sure to delete all records?')"
                    data-method="GET"
                >
                    <i class="las la-trash"></i> @lang('Delete All')
                </button>
            @endpermit
            @permit('admin.read.automation.errors')
                <button class="btn btn-sm btn-outline--primary confirmationBtn"
                    data-action="{{ route('admin.read.automation.errors') }}"
                    data-question="@lang('Are you sure to mark all as read?')"
                    data-method="GET"
                >
                    <i class="la la-eye"></i> @lang('Mark All as Read')
                </button>
            @endpermit
        </div>
    @endpush
@endif

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.deleteAll').on('click', function () {
                var modal = $('#deleteAllModal');
                modal.modal('show');
            });

            @permit('admin.delete.automation.error')
                $('.delete').on('click', function () { 
                    var modal = $('#deleteModal');
                    modal.find('form').attr('action', "{{ route('admin.delete.automation.error', '') }}"+'/'+$(this).data('id'));
                    modal.modal('show');
                });
            @endpermit
            
        })(jQuery);
    </script>
@endpush