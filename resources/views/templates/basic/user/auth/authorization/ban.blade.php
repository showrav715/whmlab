@extends($activeTemplate . 'layouts.app')
@section('app')
    @php
        $banned = @getContent('banned.content', true);
    @endphp
    <div class="section banned-section bg--light section-full">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-8 col-12 text-center">
                    <div class="ban-section">
                        <h4 class="text-center text-danger mb-4">
                            {{ __(@$banned->data_values->heading) }}
                        </h4>
                        <img src="{{ frontendImage('banned', @$banned->data_values->image) }}" alt="@lang('Ban Image')">
                        <div class="mt-4">
                            <p class="fw-bold mb-2">@lang('Reason')</p>
                            <p>{{ __($user->ban_reason) }}</p>
                        </div>
                        <a href="{{ route('home') }}" class="btn btn--base mt-4">
                            <i class="las la-undo"></i>
                            @lang('Go Back')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .banned-section {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
@endpush
