@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 bg--transparent shadow-none">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two bg-white">
                            <thead>
                            <tr>
                                <th>@lang('Product/Service')</th>
                                <th>@lang('Reason')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Cancellation By End')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead> 
                            <tbody> 
                                @forelse($cancelRequests as $data)
                                <tr>
                                    <td>
                                        <span class="fw-bold">
                                            @if($data->service)
                                                @permit('admin.order.hosting.details')
                                                    <a href="{{ route('admin.order.hosting.details', $data->service->id) }}">
                                                        {{ @$data->service->product->serviceCategory->name }}
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)">
                                                        {{ @$data->service->product->serviceCategory->name }}
                                                    </a>
                                                @endpermit
                                            @endif
                                        </span>
                                        <br> 
                                        <span class="small">
                                            @if($data->service)
                                                <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', $data->service->user_id) : 'javascript:void(0)' }}">
                                                    <span>@</span>{{ $data->service->user->username }}
                                                </a>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        {{ strLimit($data->reason, 50) }} 
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $data::type()[$data->type] }}</span>
                                    </td>
                                    <td>
                                        @if($data->type == 1)
                                            {{ showDateTime($data->created_at) }} <br> {{ diffForHumans($data->created_at) }}
                                        @else 
                                            {{ showDateTime($data->service->next_due_date) }} <br> {{ diffForHumans($data->service->next_due_date) }}
                                        @endif
                                    </td>
                                    <td>
                                        @php echo $data->showStatus; @endphp
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline--primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="las la-ellipsis-v"></i>@lang('Action')
                                        </button>
                                        <div class="dropdown-menu">
                                            <a href="javascript:void(0)" class="dropdown-item cancelRequest" data-id="{{ $data->id }}"
                                                data-modal_title="@lang('Mark as cancellation')"
                                            >
                                                <i class="la la-ban"></i> @lang('Mark as cancellation')
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item reason reasonId{{ $data->id }}" data-reason="{{ $data->reason }}"
                                                data-modal_title="@lang('Reason')"
                                            >
                                                <i class="la la-eye"></i> @lang('Reason')
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item delete" data-id="{{ $data->id }}"
                                                data-modal_title="@lang('Delete')"
                                            >
                                                <i class="la la-trash"></i> @lang('Delete')
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($cancelRequests->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($cancelRequests) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

<div id="cancelRequest" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('Mark as Cancellation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.cancel.request') }}">
                @csrf
                <input type="hidden" name="id" required> 
                <div class="modal-body">
                    <p>@lang('Are you sure to cancel this service/product')?</p>
                </div> 
                @permit('admin.cancel.request')
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                @endpermit
            </form>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('Confirmation')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.cancel.request.delete') }}">
                @csrf
                <input type="hidden" name="id" required> 
                <div class="modal-body">
                    <p>@lang('Are you sure to delete this cancellation request')?</p>
                </div> 
                @permit('admin.cancel.request.delete')
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                @endpermit
            </form>
        </div>
    </div>
</div>

<div id="reasonModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('Reason for Cancellation Request')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <div class="modal-body">
                <p class="view_reason"></p>
            </div> 
            <div class="modal-footer">
                <button type="button" class="btn btn--danger h-45 w-100" data-bs-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@if(!@$user)
    @push('breadcrumb-plugins')
        <x-search-form dateSearch='yes' placeholder='Username' />
    @endpush 
@endif

@push('style')
    <style>
        .table-responsive {
            background: transparent;
            min-height: 350px;
        }

        .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
    </style>
@endpush

@push('script')  
    <script>
        $(document).ready(function(){
            "use strict";

            $('.reason').on('click', function () { 
                var modal = $('#reasonModal');
                modal.find('.view_reason').text($(this).data('reason'));
                modal.modal('show');
            });

            $('.delete').on('click', function () {
                var modal = $('#deleteModal');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.cancelRequest').on('click', function () { 
                var modal = $('#cancelRequest');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            var id = "{{ request()->id }}";
            if(id){
                $(`.reasonId${id}`).trigger('click');
            }
 
        });
    </script>
@endpush