@extends($activeTemplate . 'layouts.side_bar')

@php 
    $getResponse = collect(@$result['data']);
@endphp

@section('data')
    <div class="col-lg-9">
        <div class="row gy-4">
            <div class="col-lg-12"> 
                <h3>@lang('Register Domain')</h3>
                <p class="mt-2">@lang('Find your new domain name. Enter your desired domain name or keyword below to check availability')...</p>
            </div>
            <div class="col-lg-12 text-center">
                @include($activeTemplate . 'partials.domain_search_form')
            </div>

            <div class="col-lg-12 text-center">
                @if($getResponse->where('domain', @$result['domain'])->where('available', true)->count())
                    <h3>
                        @lang('Congratulations')! <span class="text--base">{{ @$result['domain'] }} @lang('is')</span> @lang('available')!
                    </h3>
                @elseif($getResponse->where('domain', @$result['domain'])->where('available', false)->count())
                    <h3>
                        <span class="text--danger">{{ @$result['domain'] }} @lang('is')</span> @lang('unavailable')!
                    </h3>
                @endif
            </div>

            <div class="col-lg-12 text-center">
                @if(!@$result['isSupported'] && @$result['domain'])
                    <h3>@lang('We are not supporting ') <span class="text--warning">({{ @$result['tld'] }})</span> @lang('right now')</h3>
                @endif 
            </div>

            <div class="col-12">
                @foreach($getResponse->sortByDesc('match') as $data)  
                    <div class="domain-row">
                        <span>
                            {{ @$data['domain'] }} 
                        </span>
                        <div class="text-end">
                            @if(@$data['available'])
                                <span class="fw-bold text-end">
                                    {{ showAmount(@$data['setup']->pricing->firstPrice['price'] ?? 0) }}
                                </span>
                                <form action="{{ route('shopping.cart.add.domain') }}" method="post" class="d-inline ms-2">
                                    @csrf
                                    <input type="hidden" name="domain" required value="{{ @$data['domain'] }} ">

                                    <input type="hidden" name="domain_setup_id" required value="{{ @$data['setup']->id }}">
                                    <button class="btn btn--sm btn--base{{ @$data['domain'] != $result['domain'] ? '-outline' : null }}">
                                        <i class="la la-cart-plus"></i> @lang('Add')
                                    </button>
                                </form>
                            @else
                                <span class="text--info fw-bold">@lang('Unavailable')</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection