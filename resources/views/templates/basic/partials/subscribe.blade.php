@php
    $subscribe = @getContent('subscribe.content', true);
@endphp

<div class="subscribe">
    <button type="submit" class="subscribe-btn btn btn--base"> <span class="text">@lang('Subscribe')</span> <span class="icon"><i class="far fa-bell"></i></span></button>
    <div class="subscribe-box">
        <button class="subscribe__close"><i class="las la-times"></i></button>
        <h5 class="subscribe-box__title mb-2">{{ __(@$subscribe->data_values->heading) }}</h5>
        <p class="subscribe-box__desc mb-2">{{ __(@$subscribe->data_values->subheading) }}</p>
        <form action="#" class="subscription-form exclude">
            @csrf
            <div class="input-group">
                <input type="email" name="email" class="form-control form--control h-45" required>
                <button type="submit" class="input-group-text btn btn--base exclude">@lang('Submit')</button>
            </div>
        </form>
    </div>
</div>
 
@push('script')
    <script>
        (function($) {

            "use strict";

            var formEl = $(".subscription-form");
            formEl.on('submit', function(e) {
                e.preventDefault();
                var data = formEl.serialize();

                if (!formEl.find('input[name=email]').val()) {
                    return notify('error', 'Email field is required');
                }

                $.ajax({
                    url: "{{ route('subscribe') }}",
                    method: 'post',
                    data: data,

                    success: function(response) {
                        if (response.success) {
                            formEl.find('input[name=email]').val('')
                            notify('success', response.message);
                        } else {
                            $.each(response.error, function(key, value) {
                                notify('error', value);
                            });
                        }
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
