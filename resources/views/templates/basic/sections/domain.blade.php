@php
    $domainContent = @getContent('domain.content', true);
@endphp

<div class="col-12 py-5 bg-white">
    <div class="container px-3">
        <div class="text-center">
            <h3>{{ __(@$domainContent->data_values->heading) }}</h3>
            <p class="mb-5">{{ __(@$domainContent->data_values->subheading) }}</p>
        </div>
        <div class="row gy-4 justify-content-center">
            <div class="col-xl-8 col-lg-8">

                @include($activeTemplate . 'partials.domain_search_form')

            </div>
            <div class="col-md-6 text-center">
                {{ __(@$domainContent->data_values->text) }}
            </div>
        </div>
    </div>
</div>
