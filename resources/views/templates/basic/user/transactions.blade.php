@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
          
        <div class="mb-4">
            <form action=""> 
                <div class="d-flex flex-wrap gap-4">
                    <div class="flex-grow-1 form-group"> 
                        <label class="label">@lang('Transaction Number')</label>
                        <input type="text" name="search" value="{{ request()->search }}" class="form-control form--control h-45">
                    </div>
                    <div class="flex-grow-1 select2-parent">
                        <label class="form-label d-block">@lang('Type')</label>
                        <select name="trx_type" class="form-select form--control h-45 select2-basic" data-minimum-results-for-search="-1">
                            <option value="">@lang('All')</option>
                            <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                            <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                        </select>
                    </div>
                    <div class="flex-grow-1 select2-parent">
                        <label class="form-label d-block">@lang('Remark')</label>
                        <select class="form-select form--control h-45 select2-basic" data-minimum-results-for-search="-1" name="remark">
                            <option value="">@lang('All')</option>
                            @foreach($remarks as $remark)
                            <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-grow-1 form-group align-self-end"> 
                        <button class="btn btn--base w-100"><i class="las la-filter"></i> @lang('Filter')</button>
                    </div>
                </div>  
            </form>
        </div>
        
        <table class="table table--responsive--lg">
            <thead>
                <tr>
                    <th>@lang('Trx')</th>
                    <th>@lang('Transacted')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Post Balance')</th>
                    <th>@lang('Details')</th>
                </tr> 
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    <tr>
                        <td>
                            {{ $trx->trx }}
                        </td>
                        <td>
                            <div>
                                {{ showDateTime($trx->created_at, 'M d, Y, h:i a') }}
                                <br>
                                {{ diffForHumans($trx->created_at) }}
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text--{{ $trx->trx_type == '+' ? 'success' : 'danger' }}">
                                {{ $trx->trx_type }}{{showAmount($trx->amount)}}
                            </span>
                        </td>
                        <td>
                            {{ showAmount($trx->post_balance) }}
                        </td>
                        <td>
                            {{ __($trx->details) }}
                        </td>
                    </tr>
                @empty
                    <x-empty-message table={{ true }} />
                @endforelse
            </tbody>
        </table>
        @if($transactions->hasPages())
            <div class="mt-5">
                {{ paginateLinks($transactions) }}
            </div>
        @endif
    </div>
</div>
@endsection