<form action="" class="form">
    <div class="form-group position-relative mb-0">
        <div class="domain-search-icon"><i class="fas fa-search"></i></div>
        <input class="form-control form--control" type="text" name="domain" required placeholder="@lang('Domain name or keyword')" value="{{ @request()->domain }}">
        <div class="domain-search-icon-reset">
            <button class="btn btn--base" type="submit">@lang('Search')</button>
        </div>
    </div>
</form>

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.form').on('submit', function(e) {
                e.preventDefault();
                var domain = $(this).find('input[name=domain]').val();
                window.location.href = "{{ route('register.domain') }}?domain=" + domain;
            })
        })(jQuery);
    </script>
@endpush
