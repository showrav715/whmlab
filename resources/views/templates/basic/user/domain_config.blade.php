@extends($activeTemplate . 'layouts.side_bar')

@section('data')
<div class="col-lg-9">
    <form action="{{ route('shopping.cart.config.domain.update') }}" method="post">
        @csrf
        <input type="hidden" name="cart_id" required value="{{ $cart->id }}">
        <input type="hidden" name="domain_setup_id" required value="{{ $cart->domain_setup_id }}">
        <div class="container px-3">
            <div class="row gy-4">
                @php $pricing = $domainSetup->pricing; @endphp 

                <div class="col-lg-12">
                    <div class="row gy-3">
 
                        <div class="col-lg-12">
                            <h3>{{ __($pageTitle) }} <small> - ({{ @$cart->domain }})</small></h3> 
                            <p>@lang('Please review your domain name selections and any addons that are available for them')</p>
                        </div> 

                        <div class="col-xl-4 col-lg-6 col-md-6 form-group {{ $pricing->one_year_price >= 0 ? '' : 'd-none' }}">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <input type="radio" name="reg_period" id="one_year_price" value="1" required>
                                        <label for="one_year_price">@lang('One year')</label>
                                        <small>({{ showAmount($pricing->one_year_price) }} {{ gs('cur_text') }})</small>
                                    </div>
                                    <div>
                                        <input type="radio" value="1" id="one_year_id_protection" name="id_protection" disabled>
                                        <label for="one_year_id_protection">@lang('With ID Protection')</label>
                                        <small class="d-block">({{ showAmount($pricing->one_year_id_protection) }} {{ gs('cur_text') }})</small>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 form-group {{ $pricing->two_year_price >= 0 ? '' : 'd-none' }}">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <input type="radio" name="reg_period" id="two_year_price" value="2" required> 
                                        <label for="two_year_price">@lang('Two year')</label>
                                        <small>({{ showAmount($pricing->two_year_price) }} {{ gs('cur_text') }})</small>
                                    </div>
                                    <div>
                                        <input type="radio" value="2" id="two_year_id_protection" name="id_protection" disabled>
                                        <label for="two_year_id_protection">@lang('With ID Protection')</label>
                                        <small class="d-block">({{ showAmount($pricing->two_year_id_protection) }} {{ gs('cur_text') }})</small>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 form-group {{ $pricing->three_year_price >= 0 ? '' : 'd-none' }}">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <input type="radio" name="reg_period" id="three_year_price" value="3" required> 
                                        <label for="three_year_price">@lang('Three year')</label>
                                        <small>({{ showAmount($pricing->three_year_price) }} {{ gs('cur_text') }})</small>
                                    </div>
                                    <div>
                                        <input type="radio" value="3" id="three_year_id_protection" name="id_protection" disabled>
                                        <label for="three_year_id_protection">@lang('With ID Protection')</label>
                                        <small class="d-block">({{ showAmount($pricing->three_year_id_protection) }} {{ gs('cur_text') }})</small>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 form-group {{ $pricing->four_year_price >= 0 ? '' : 'd-none' }}">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <input type="radio" name="reg_period" id="four_year_price" value="4" required> 
                                        <label for="four_year_price">@lang('Four year')</label>
                                        <small>({{ showAmount($pricing->four_year_price) }} {{ gs('cur_text') }})</small>
                                    </div>
                                    <div>
                                        <input type="radio" value="4" id="four_year_id_protection" name="id_protection" disabled>   
                                        <label for="four_year_id_protection">@lang('With ID Protection')</label>
                                        <small class="d-block">({{ showAmount($pricing->four_year_id_protection) }} {{ gs('cur_text') }})</small>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 form-group {{ $pricing->five_year_price >= 0 ? '' : 'd-none' }}">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <input type="radio" name="reg_period" id="five_year_price" value="5" required> 
                                        <label for="five_year_price">@lang('Five year')</label>
                                        <small>({{ showAmount($pricing->five_year_price) }} {{ gs('cur_text') }})</small>
                                    </div>
                                    <div>
                                        <input type="radio" value="5" id="five_year_id_protection" name="id_protection" disabled>  
                                        <label for="five_year_id_protection">@lang('With ID Protection')</label>
                                        <small class="d-block">({{ showAmount($pricing->five_year_id_protection) }} {{ gs('cur_text') }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6 form-group {{ $pricing->six_year_price >= 0 ? '' : 'd-none' }}">
                            <div class="card">
                                <div class="card-body">
                                    <div>
                                        <input type="radio" name="reg_period" id="six_year_price" value="6" required> 
                                        <label for="six_year_price">@lang('Six year')</label>
                                        <small>({{ showAmount($pricing->six_year_price) }} {{ gs('cur_text') }})</small>
                                    </div>
                                    <div>
                                        <input type="radio" value="6" id="six_year_id_protection" name="id_protection" disabled>
                                        <label for="six_year_id_protection">@lang('With ID Protection')</label>
                                        <small class="d-block">({{ showAmount($pricing->six_year_id_protection) }} {{ gs('cur_text') }})</small>
                                    </div>
                                </div> 
                            </div>
                        </div> 
          
                        @if(!$cart->product_id)
                            <div class="col-md-12 mt-4">
                                <h5 class="text-center mb-1">@lang('Nameservers')</h5>
                                <div class="row gy-3">
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label>@lang('Nameserver 1')</label>
                                            <input type="text" name="ns1" class="form-control form--control h-45" value="{{ $cart->ns1 ?? @$cart->domainRegister->ns1 }}">
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label>@lang('Nameserver 2')</label>
                                            <input type="text" name="ns2" class="form-control form--control h-45" value="{{ $cart->ns2 ?? @$cart->domainRegister->ns2 }}">
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label>@lang('Nameserver 3')</label>
                                            <input type="text" name="ns3" class="form-control form--control h-45" value="{{ ($cart->ns1 && $cart->ns2) ? $cart->ns3 : @$cart->domainRegister->ns3 }}">
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="form-group"> 
                                            <label>@lang('Nameserver 4')</label> 
                                            <input type="text" name="ns4" class="form-control form--control h-45" value="{{ ($cart->ns1 && $cart->ns2) ? $cart->ns4 : @$cart->domainRegister->ns4 }}">
                                        </div>
                                    </div> 
                                </div>
                            </div> 
                        @endif

                        <div class="col-md-12 form-group text-center mt-4"> 
                            <button type="submit" class="btn bg--base btn-lg text-white w-100">
                                @lang('Continue') <i class="la la-arrow-circle-right"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form> 
</div> 
@endsection

@push('script')
<script>
    (function ($) { 
        "use strict"; 

        $(`input[name='reg_period'][value=@json($cart->reg_period)]`).prop('checked', true);
        $(`input[name='id_protection'][value=@json($cart->reg_period)]`).prop('disabled', false);

        if( @json($cart->id_protection) ){
            $(`input[name='id_protection'][value=@json($cart->reg_period)]`).prop('checked', true).attr('isChecked', 'checked');
        }

        $('input[name=reg_period]').on('click', function(){

            $('input[name=id_protection]').each(function(){ 
                $(this).prop('checked', false).prop('disabled', true);
            });

            if($(this).prop('checked') == true){
                $(this).parents('.card-body').find('input[type=radio]').last().prop('disabled', false);
            }
        });

        $('input[name=id_protection]').on('click', function(){
      
            var data = $(this).attr('isChecked');

            if(data != 'checked'){
                $(this).attr('isChecked', 'checked');
                return $(this).prop('checked', true);
            }

            $(this).attr('isChecked', 'unChecked');
            return $(this).prop('checked', false);

        });

    })(jQuery);
</script>
@endpush
