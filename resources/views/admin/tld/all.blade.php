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
                            <th>@lang('Extension')</th>
                            <th>@lang('ID Protection')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($domains as $domain)  
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $domain->extension }}</span>
                                    </td>
                                    <td>
                                        @php echo $domain->showIdProtection; @endphp
                                    </td>
                                    <td>
                                        @php echo $domain->showStatus; @endphp
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline--primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="las la-ellipsis-v"></i>@lang('Action')
                                        </button>
                                        <div class="dropdown-menu">
                                            @permit('admin.tld.update.pricing')
                                                <a href="javascript:void(0)" class="dropdown-item priceModal" 
                                                    data-modal_title="@lang('Pricing')"
                                                    data-pricing="{{ $domain->pricing }}" data-id="{{ @$domain->pricing->id }}" data-ex="{{ $domain->extension }}"
                                                >
                                                    <i class="la la-money-bill-wave"></i> @lang('Pricing')
                                                </a>
                                            @endpermit

                                            @permit('admin.tld.update')
                                                <a href="javascript:void(0)" class="dropdown-item editBtn" data-data="{{ $domain }}" 
                                                    data-modal_title="@lang('Edit')"
                                                >
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @endpermit

                                            @permit('admin.tld.status')
                                                @if($domain->status == 0)
                                                    <a href="javascript:void(0)"
                                                            class="dropdown-item confirmationBtn"
                                                            data-action="{{ route('admin.tld.status', $domain->id) }}"
                                                            data-question="@lang('Are you sure to enable this TLD?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                    data-action="{{ route('admin.tld.status', $domain->id) }}"
                                                    data-question="@lang('Are you sure to disable this TLD?')">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @else 
                                                @if($domain->status == 0)
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
            @if($domains->hasPages())
                <div class="card-footer py-4">
                    <div class="card-footer py-4">
                        {{ paginateLinks($domains) }}
                    </div>
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
                <h5 class="modal-title" id="createModalLabel">@lang('Domain Extension/TLD')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.tld.add') }}">
                @csrf 
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Extension')</label>
                                <input type="text" class="form-control" name="extension" value="{{old('extension')}}" required autocomplete="off">
                            </div>
                        </div> 
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('ID Protection')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="50" data-on="@lang('Enable')" data-off="@lang('Disable')" name="id_protection" checked>
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

{{-- EDIT MODAL --}}  
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('Update Domain Extension/TLD')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.tld.update') }}">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Extension')</label>
                                <input type="text" class="form-control" name="extension" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('ID Protection')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="id_protection">
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

{{-- PRICE MODAL --}}  
<div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Domain/TLD Pricing') <span class="domainExtension"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="{{ route('admin.tld.update.pricing') }}" method="POST">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold">
                                        <label for="one_active">@lang('One Year')</label>
                                        <input type="checkbox" name="one_active" id="one_active" class="price_active">
                                    </h6>
                                </div>
                                <div class="row one_active">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="one_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="one_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Renewal')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="one_year_renew" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4  mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold">
                                        <label for="two_active">@lang('Two Year')</label>
                                        <input type="checkbox" name="two_active" id="two_active" class="price_active">
                                    </h6>
                                </div>
                                <div class="row two_active">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="two_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="two_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Renewal')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="two_year_renew" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold">
                                        <label for="three_active">@lang('Three Year')</label>
                                        <input type="checkbox" name="three_active" id="three_active" class="price_active">
                                    </h6>
                                </div>
                                <div class="row three_active">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="three_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="three_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Renewal')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="three_year_renew" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold">
                                        <label for="four_active">@lang('Four Year')</label>
                                        <input type="checkbox" name="four_active" id="four_active" class="price_active">
                                    </h6>
                                </div>
                                <div class="row four_active">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="four_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="four_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Renewal')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="four_year_renew" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold">
                                        <label for="five_active">@lang('Five Year')</label>
                                        <input type="checkbox" name="five_active" id="five_active" class="price_active">
                                    </h6>
                                </div>
                                <div class="row five_active">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="five_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="five_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Renewal')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="five_year_renew" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mt-4">
                            <div class="custom-pricing">
                                <div class="border-line-area">
                                    <h6 class="border-line-title text-center fw-bold">
                                        <label for="six_active">@lang('Six Year')</label>
                                        <input type="checkbox" name="six_active" id="six_active" class="price_active">
                                    </h6>
                                </div>
                                <div class="row six_active">
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="six_year_price" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12 col-xl-6">
                                        <div class="form-group">
                                            <label>@lang('ID Protection')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="six_year_id_protection" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>@lang('Renewal')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input type="number" class="form-control" placeholder="0" name="six_year_renew" step="any" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

@permit('admin.tld.add')
    @push('breadcrumb-plugins') 
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endpush
@endpermit

@push('style')
    <style>
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

            $('.addBtn').on('click', function () {
                var modal = $('#createModal'); 
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var record = $(this).data('data');
                
                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=extension]').val(record.extension);

                if(record.id_protection == 1){
                    modal.find('input[name=id_protection]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=id_protection]').bootstrapToggle('off');
                }

                modal.modal('show');
            });

            $('.priceModal').on('click', function () {
                var modal = $('#priceModal');
                
                modal.find('.domainExtension').text($(this).data('ex'));
                modal.find('form')[0].reset();     
 
                var pricing = $(this).data('pricing');
                modal.find('input[name=id]').val($(this).data('id'));

                modal.find('input[name=one_year_price]').val(parseFloat(pricing.one_year_price).toFixed(2));
                modal.find('input[name=one_year_id_protection]').val(parseFloat(pricing.one_year_id_protection).toFixed(2));
                modal.find('input[name=one_year_renew]').val(parseFloat(pricing.one_year_renew).toFixed(2));

                priceToogle(pricing.one_year_price, 'one_active');

                
                modal.find('input[name=two_year_price]').val(parseFloat(pricing.two_year_price).toFixed(2));
                modal.find('input[name=two_year_id_protection]').val(parseFloat(pricing.two_year_id_protection).toFixed(2));
                modal.find('input[name=two_year_renew]').val(parseFloat(pricing.two_year_renew).toFixed(2));

                priceToogle(pricing.two_year_price, 'two_active');

                modal.find('input[name=three_year_price]').val(parseFloat(pricing.three_year_price).toFixed(2));
                modal.find('input[name=three_year_id_protection]').val(parseFloat(pricing.three_year_id_protection).toFixed(2));
                modal.find('input[name=three_year_renew]').val(parseFloat(pricing.three_year_renew).toFixed(2));

                priceToogle(pricing.three_year_price, 'three_active');

                modal.find('input[name=four_year_price]').val(parseFloat(pricing.four_year_price).toFixed(2));
                modal.find('input[name=four_year_id_protection]').val(parseFloat(pricing.four_year_id_protection).toFixed(2));
                modal.find('input[name=four_year_renew]').val(parseFloat(pricing.four_year_renew).toFixed(2));

                priceToogle(pricing.four_year_price, 'four_active');

                modal.find('input[name=five_year_price]').val(parseFloat(pricing.five_year_price).toFixed(2));
                modal.find('input[name=five_year_id_protection]').val(parseFloat(pricing.five_year_id_protection).toFixed(2));
                modal.find('input[name=five_year_renew]').val(parseFloat(pricing.five_year_renew).toFixed(2));

                priceToogle(pricing.five_year_price, 'five_active');

                modal.find('input[name=six_year_price]').val(parseFloat(pricing.six_year_price).toFixed(2));
                modal.find('input[name=six_year_id_protection]').val(parseFloat(pricing.six_year_id_protection).toFixed(2));
                modal.find('input[name=six_year_renew]').val(parseFloat(pricing.six_year_renew).toFixed(2));

                priceToogle(pricing.six_year_price, 'six_active');

                modal.modal('show');
            });
  
            $('.price_active').on('click', function(){
                var selectorName = $(this).prop('name');

                if($(this).prop('checked') == true){ 
                    $('.'+selectorName).removeClass('d-none');
                    $(`.${selectorName} :input`).first().val(0);
                }else{
                    $('.'+selectorName).addClass('d-none');
                    $(`.${selectorName} :input`).first().val(-1);
                }
            }); 

            function priceToogle(price, selectorName){
                if(price >= 0){
                    $('#'+selectorName).prop('checked', true);
                    $('.'+selectorName).removeClass('d-none');
                }else{
                    $('.'+selectorName).addClass('d-none');
                    $('#'+selectorName).prop('checked', false);
                }
            }

        })(jQuery);
    </script>
@endpush

  