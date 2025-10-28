@if(($cookie->data_values->status == Status::ENABLE) && !\Cookie::get('gdpr_cookie'))
    <!-- cookies dark version start -->
    <div class="cookies-card text-center hide">
        <div class="cookies-card__icon bg--base">
            <i class="las la-cookie-bite"></i>
        </div>
        <p class="mt-4 cookies-card__content">
            {{ $cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}" target="_blank" class="text--primary">@lang('learn more')</a>
        </p>
        <div class="cookies-card__btn mt-4">
            <button type="button" class="btn btn--base w-100 policy">@lang('Allow')</button>
        </div>
    </div>
    <!-- cookies dark version end -->
@endif
 
@push('script')
<script> 
(function ($) { 
    "use strict";

    $('.policy').on('click',function(){
        $.get('{{route('cookie.accept')}}', function(response){
            $('.cookies-card').addClass('d-none');
        });
    });

    setTimeout(function(){
        $('.cookies-card').removeClass('hide')
    },2000);
    
})(jQuery);
</script>
@endpush