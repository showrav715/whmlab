@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 "> 
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead> 
                        <tbody> 
                            @forelse($groups as $group)
                                <tr> 
                                    <td>
                                        <span class="fw-bold">{{ __($group->name) }}</span>
                                    </td>

                                    <td>
                                        @php echo getProductModuleLogo($group->type); @endphp
                                    </td>
                                    
                                    <td>
                                        @php echo $group->showStatus; @endphp
                                    </td>

                                    <td>
                                        <div class="button--group">
                                            @permit('admin.group.server.update')
                                                <button class="btn btn-sm btn-outline--primary editBtn" data-data="{{ $group }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>
                                            @else 
                                                <button class="btn btn-sm btn-outline--primary" disabled>
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>
                                            @endpermit

                                            @permit('admin.group.server.status')
                                                @if($group->status == 0)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline--success confirmationBtn"
                                                            data-action="{{ route('admin.group.server.status', $group->id) }}"
                                                            data-question="@lang('Are you sure to enable this server group?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.group.server.status', $group->id) }}"
                                                    data-question="@lang('Are you sure to disable this server group?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            @else 
                                                @if($group->status == 0)
                                                    <button type="button" class="btn btn-sm btn-outline--success" disabled>
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger" disabled>
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
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
            @if ($groups->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($groups) }}
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
                <h5 class="modal-title" id="createModalLabel">@lang('New Server Group')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.group.server.add') }}">
                @csrf 
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Type')</label>
                        <select name="type" class="form-control" required>
                            <option value="">@lang('Select One')</option>
                            @foreach (productModule() as $index => $type)
                                @if($index == 0)
                                    @continue;
                                @endif
                                <option value="{{ $index }}">{{ __($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required value="{{old('name')}}">
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
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('Update Server Group')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.group.server.update') }}">
                @csrf
                <input type="hidden" name="id" required> 
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Type')</label>
                        <select name="type" class="form-control" required>
                            <option value="">@lang('Select One')</option>
                            @foreach (productModule() as $index => $type)
                                @if($index == 0)
                                    @continue;
                                @endif
                                <option value="{{ $index }}">{{ __($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Name')</label> 
                        <input type="text" class="form-control" name="name" required>
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
<div class="d-flex justify-content-end d-flex flex-wrap gap-2">
    @permit('admin.server.add.page')
        <a class="btn btn-sm btn-outline--success" href="{{ route('admin.server.add.page') }}">
            <i class="las la-plus"></i>@lang('Add Server')
        </a>
    @endpermit
    @permit('admin.group.server.add')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i>@lang('Add Group')
        </button>
    @endpermit
</div>
@endpush


@push('script')  
    <script>
        (function($){
            "use strict";

            $('.addBtn').on('click', function () {
                var modal = $('#createModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var record = $(this).data('data');
                var groupsId = [];

                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=name]').val(record.name);
                modal.find('select[name=type]').val(record.type);

                modal.modal('show');
            });
 
        })(jQuery);
    </script>
@endpush


 