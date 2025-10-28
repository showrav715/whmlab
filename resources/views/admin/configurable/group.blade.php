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
                            <th>@lang('Group Name')</th>
                            <th>@lang('Configurable Options')</th>
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
                                        {{ @$group->options->count() }}
                                    </td>

                                    <td>
                                        @php echo $group->showStatus; @endphp
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline--primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="las la-ellipsis-v"></i>@lang('Action')
                                        </button>
                                        <div class="dropdown-menu">
                                            @permit('admin.configurable.group.options')
                                                <a href="{{ route('admin.configurable.group.options', $group->id) }}" class="dropdown-item" 
                                                    data-modal_title="@lang('Configurable Options')"
                                                >
                                                    <i class="la la-clipboard"></i> @lang('Configurable Options')
                                                </a>
                                            @endpermit

                                            @permit('admin.configurable.group.update')
                                                <a href="javascript:void(0)" class="dropdown-item editBtn" data-data="{{ $group }}" 
                                                    data-modal_title="@lang('Edit')"
                                                >
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @endpermit

                                            @permit('admin.configurable.group.status')
                                                @if($group->status == 0)
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                        data-action="{{ route('admin.configurable.group.status', $group->id) }}"
                                                        data-question="@lang('Are you sure to enable this configurable group?')"
                                                        data-modal_title="@lang('Enable')"
                                                    >
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                        data-action="{{ route('admin.configurable.group.status', $group->id) }}"
                                                        data-question="@lang('Are you sure to disable this configurable group?')"
                                                        data-modal_title="@lang('Disable')"
                                                    >
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @else 
                                                @if($group->status == 0)
                                                    <a href="javascript:void(0)" class="dropdown-item" data-modal_title="@lang('Enable')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item" data-modal_title="@lang('Disable')">
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
                <h5 class="modal-title" id="createModalLabel">@lang('New Configurable Group')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.add') }}">
                @csrf 
                <div class="modal-body"> 
                    <div class="form-group">
                        <label>@lang('Name')</label> 
                        <input type="text" class="form-control" name="name" required value="{{old('name')}}" required>
                    </div>
                    <div class="form-group"> 
                        <label>@lang('Assigned Products')</label>
                        <select name="assigned_product[]" class="form-control select-h-custom productsId select2-basic" multiple="multiple">
                             @foreach($products as $product) 
                                <option value="{{ $product->id }}">{{ __($product->name) }} - {{ __(@$product->serviceCategory->name) }}</option>
                             @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="description" class="form-control" required rows="4">{{old('description')}}</textarea>
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
                <h5 class="modal-title" id="createModalLabel">@lang('Update Configurable Group')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.configurable.group.update') }}">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body"> 
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control" name="name" required required>
                    </div>
                    <div class="form-group">
                        <label>@lang('Assigned Products')</label>
                        <select name="assigned_product[]" class="form-control select-h-custom productsId select2-basic" multiple="multiple">
                             @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ __($product->name) }} - {{ __(@$product->serviceCategory->name) }}</option>
                             @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="description" class="form-control" required rows="4"></textarea>
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

@permit('admin.configurable.group.add')
    @push('breadcrumb-plugins')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endpush
@endpermit

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
                
                if(record.get_products){
                    var productsId = []; 
                    for(var i = 0; i < record.get_products.length; i++){
                        productsId[i] = record.get_products[i].product_id; 
                    }
        
                    modal.find('.productsId').val(productsId).select2();
                } 
               
                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=name]').val(record.name);
                modal.find('textarea[name=description]').val(record.description);
                modal.find('select[name=service_category_id]').val(record.service_category_id);

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .select-h-custom{
            height: 110px !important;
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