@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 bg--transparent shadow-none">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two bg-white">
                        <thead>
                        <tr>
                            <th>@lang('Configurable Option Name')</th>
                            <th>@lang('Configurable Sub Option')</th>
                            <th>@lang('Sort Order')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead>
                        <tbody>
                            @forelse($options as $option)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ __($option->name) }}</span>
                                    </td>

                                    <td>
                                        {{ @$option->subOptions->count() }}
                                    </td>

                                    <td>
                                       {{ $option->order }}
                                    </td>
                                
                                    <td>
                                        @php echo $option->showStatus; @endphp
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline--primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="las la-ellipsis-v"></i>@lang('Action')
                                        </button>
                                        <div class="dropdown-menu">
                                            @permit('admin.configurable.group.sub.options')
                                                <a href="{{ route('admin.configurable.group.sub.options', [$group->id, $option->id]) }}" class="dropdown-item" 
                                                    data-modal_title="@lang('Configurable Sub options')"
                                                >
                                                    <i class="la la-list"></i> @lang('Configurable Sub options')
                                                </a>
                                            @endpermit

                                            @permit('admin.configurable.group.update.option')
                                                <a href="javascript:void(0)" class="dropdown-item editBtn" data-data="{{ $option }}" 
                                                    data-modal_title="@lang('Edit')"
                                                >
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @endpermit

                                            @permit('admin.configurable.group.option.status')
                                                @if($option->status == 0)
                                                    <a href="javascript:void(0)"
                                                        class="dropdown-item confirmationBtn"
                                                        data-action="{{ route('admin.configurable.group.option.status', $option->id) }}"
                                                        data-question="@lang('Are you sure to enable this configurable option?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                    data-action="{{ route('admin.configurable.group.option.status', $option->id) }}"
                                                    data-question="@lang('Are you sure to disable this configurable option?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @else 
                                                @if($option->status == 0)
                                                    <a href="javascript:void(0)" class="dropdown-item">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @endpermit
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
            @if ($options->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($options) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />

{{-- NEW MODAL --}}
<div id="createModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('New Option')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.add.option') }}">
                @csrf
                <input type="hidden" value="{{ $group->id }}" required name="group_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{old('name')}}" required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Sort Order')</label>
                        <input type="number" class="form-control" name="order" required value="{{old('order') ?? 0}}" required>
                    </div>
                    <div class="form-group d-none">
                        <label>@lang('Option Type')</label>
                        <select name="option_type" class="form-control">
                            <option value="1">@lang('Dropdown')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('Update Option')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.update.option') }}">
                @csrf
                <input type="hidden" value="{{ $group->id }}" required name="group_id">
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control edit_name" name="name" required required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Sort Order')</label>
                        <input type="number" class="form-control" name="order" required required>
                    </div>
                    <div class="form-group d-none">
                        <label>@lang('Option Type')</label>
                        <select name="option_type" class="form-control">
                            <option value="1">@lang('Dropdown')</option>
                        </select>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<div class="d-flex justify-content-end flex-wrap gap-2">
    @permit('admin.configurable.groups')
        <a href="{{ route('admin.configurable.groups') }}" class="btn btn-sm btn-outline--dark">
            <i class="la la-undo"></i> @lang('Go to Groups')
        </a>
    @endpermit
    @permit('admin.configurable.group.add.option')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endpermit
</div>
@endpush

@push('style')
<style>
@media only screen and (max-width: 400px) {
    [data-label]::before {            
    max-width: 160px;
    text-align: left;
    }
}
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
        (function($){
            "use strict";
 
            var pageTitle = '{{ $pageTitle }}';
            var groupName = pageTitle.substring(pageTitle.indexOf('for') + 4);
            var withOutGroup = pageTitle.split(groupName)[0];
            pageTitle = `${withOutGroup}<span class='text--primary'>${groupName}</span>`; 
            $('.page-title').html(pageTitle);

            $('.addBtn').on('click', function () {
                var modal = $('#createModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var record = $(this).data('data');
             
                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=name]').val(record.name);
                modal.find('select[name=option_type]').val(record.option_type);
                modal.find('input[name=order]').val(record.order);

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

  