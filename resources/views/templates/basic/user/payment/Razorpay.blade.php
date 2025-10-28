@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card custom--card style-two">
                    <div class="card-header">
                        <h6 class="card-title text-center">{{ __($pageTitle) }}</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush text-center">
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You have to pay ')
                                <strong>{{showAmount($deposit->final_amount,currencyFormat:false)}} {{__($deposit->method_currency)}}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You will get ')
                                <strong>{{showAmount($deposit->amount)}}</strong>
                            </li>
                        </ul>
                         <form action="{{$data->url}}" method="{{$data->method}}">
                            <input type="hidden" custom="{{$data->custom}}" name="hidden">
                            <script src="{{$data->checkout_js}}"
                                    @foreach($data->val as $key=>$value)
                                    data-{{$key}}="{{$value}}"
                                @endforeach >
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        (function ($) {
            "use strict";
            $('input[type="submit"]').addClass("mt-4 btn btn--base w-100");
        })(jQuery);
    </script>
@endpush
