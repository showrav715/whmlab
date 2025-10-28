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
                            <th>@lang('Coupon Code')</th>
                            <th>@lang('Discount')</th>
                            <th>@lang('Used')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody> 
                        @forelse($coupons as $data)
                        <tr>

                            <td>
                                <span class="fw-bold">{{ __($data->name) }}</span>
                            </td>

                            <td>
                                {{ __($data->code) }}
                            </td>

                            <td>
                                @if($data->type == 0)
                                    {{ showAmount($data->discount, 2) }}%
                                @else
                                    {{ showAmount($data->discount, 2) }} {{ __(gs('cur_text')) }}
                                @endif
                            </td>

                            <td>
                                <span class="fw-bold">{{ $data->used }}</span> @lang('Times')
                            </td>

                            <td>
                                @php echo $data->showStatus; @endphp
                            </td>

                            <td>
                                <div class="button--group">
                                    @permit('admin.coupon.update')
                                        <button class="btn btn-sm btn-outline--primary editBtn" data-row="{{ $data }}">
                                            <i class="la la-pencil"></i> @lang('Edit')
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline--primary" disabled>
                                            <i class="la la-pencil"></i> @lang('Edit')
                                        </button>
                                    @endpermit
                                    @permit('admin.coupon.status')
                                        @if($data->status == 0)
                                            <button type="button"
                                                    class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.coupon.status', $data->id) }}"
                                                    data-question="@lang('Are you sure to enable this coupon?')">
                                                <i class="la la-eye"></i> @lang('Enable')
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                            data-action="{{ route('admin.coupon.status', $data->id) }}"
                                            data-question="@lang('Are you sure to disable this coupon?')">
                                                    <i class="la la-eye-slash"></i> @lang('Disable')
                                            </button>
                                        @endif
                                    @else
                                        @if($data->status == 0)
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
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}!</td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($coupons->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($coupons) }}
                </div>
            @endif
        </div>
    </div>
</div> 

<x-confirmation-modal />

{{-- ADD METHOD MODAL --}}
<div id="addModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('New Coupon')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.coupon.add') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="name">@lang('Name')</label>
                            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="code">@lang('Coupon Code')</label>
                            <input type="text" name="code" id="code" class="form-control" required value="{{ old('code') }}">
                        </div>
                        <div class="col-lg-12 form-group">
                            <label>@lang('Discount Type')</label>
                            <select name="type" class="form-control addType" required>
                                <option value="0" {{ old('type') == 0 ? 'selected' : null }}>@lang('Percentage')</option>
                                <option value="1" {{ old('type') == 1 ? 'selected' : null }}>@lang('Fixed')</option>
                            </select>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="discount">@lang('Discount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="discount" id="discount" class="form-control" required value="{{ old('discount') }}">
                                <span class="input-group-text addText">%</span>
                            </div>
                        </div>
                        <div class="col-lg-12 form-group"> 
                            <label for="min_order_amount">@lang('Minmum Order Amount')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="min_order_amount" id="min_order_amount" class="form-control" required value="{{ old('min_order_amount') }}">
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT METHOD MODAL --}}
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Update Coupon')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.coupon.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 form-group">
                            <label for="name">@lang('Name')</label>
                            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="code">@lang('Coupon Code')</label>
                            <input type="text" name="code" id="code" class="form-control" required value="{{ old('code') }}">
                        </div>
                        <div class="col-lg-12 form-group">
                            <label>@lang('Amount Type')</label>
                            <select name="type" class="form-control updateType" required>
                                <option value="0">@lang('Percentage')</option>
                                <option value="1">@lang('Fixed')</option>
                            </select>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="discount">@lang('Discount')</label>
                            <div class="input-group">
                                <input type="text" name="discount" id="discount" class="form-control" required value="{{ old('discount') }}">
                                <span class="input-group-text updateText">%</span>
                            </div>
                        </div>
                        <div class="col-lg-12 form-group">
                            <label for="min_order_amount">@lang('Minmum Order Amount')</label>
                            <div class="input-group">
                                <input type="text" name="min_order_amount" id="min_order_amount" class="form-control" required value="{{ old('min_order_amount') }}">
                                <span class="input-group-text">{{ __(gs('cur_text')) }}</span>
                            </div>
                        </div>
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

@permit('admin.coupon.add')
    @push('breadcrumb-plugins')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endpush
@endpermit

@push('script')
    <script>
        (function ($) {
            "use strict";

            $('.addBtn').on('click', function () {
                var modal = $('#addModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var row = $(this).data('row'); 

                modal.find('input[name=name]').val(row.name);
                modal.find('input[name=id]').val(row.id);
                modal.find('input[name=code]').val(row.code);
                modal.find('input[name=discount]').val(parseFloat(row.discount).toFixed(2));
                modal.find('input[name=min_order_amount]').val(parseFloat(row.min_order_amount).toFixed(2));
                modal.find('select[name=type]').val(row.type);

                if(row.type == 0){
                    modal.find('.updateText').text('%');
                }else{
                    modal.find('.updateText').text(@json(__(gs('cur_text'))));
                }

                modal.modal('show');
            });

            $('.addType').on('change', function(){
                var value = $(this).val();
                var discount = $('.addText');

                if(value == 0){
                    discount.text('%');
                }else{
                    discount.text(@json(__(gs('cur_text'))));
                }
            });

            $('.updateType').on('change', function(){
                var value = $(this).val();
                var discount = $('.updateText');

                if(value == 0){
                    discount.text('%');
                }else{
                    discount.text(@json(__(gs('cur_text'))));
                }
            });

        })(jQuery);
    </script>
@endpush
