@extends($activeTemplate . 'layouts.frontend')

@section('content')
<div class="col-12 service-category bg--light section-full">
    <div class="container px-3">  
        <div class="row gy-2"> 
        
            <div class="col-lg-3">
                <div class="collapable-sidebar">
                    <div class="collapable-sidebar__inner">
                        <button type="button" class="collapable-sidebar__close d-lg-none d-block"><i class="las la-times"></i> </button>
                        <nav id="categoryMenu" class="collapse d-block sidebar collapse bg-white border">
                            <span class="border-bottom p-2 bg-dark-two text-center fw-bold w-100">@lang('Service Categories')</span>
                            <div class="position-sticky">
                                <div class="list-group list-group-flush">
                                    @foreach ($serviceCategories as $category)
                                        <a href="{{ route('service.category', $category->slug) }}" class="list-group-item list-group-item-action py-2 ripple" data-slug="{{ $category->slug }}">
                                            <span>{{ __($category->name) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </nav>
                        <nav id="actionMenu" class="collapse d-block sidebar collapse bg-white border mt-4">
                            <span class="border-bottom p-2 bg-dark-two text-center fw-bold w-100">@lang('Actions')</span>
                            <div class="position-sticky">
                                <div class="list-group list-group-flush">
                                    <a href="{{ route('register.domain') }}" class="list-group-item list-group-item-action py-2 ripple">
                                        <span>@lang('Register New Domain')</span>
                                    </a>
                                    <a href="{{ route('shopping.cart') }}" class="list-group-item list-group-item-action py-2 ripple">
                                        <span>@lang('View Cart')</span>
                                    </a>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="row d-lg-none d-block">
                <div class="col-12 ">
                    <div class="show-sidebar-bar">
                        <i class="las la-bars"></i>
                    </div>
                </div>
            </div> 

            @yield('data')

        </div>
    </div> 
</div>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {

            $('#categoryMenu a[data-slug="{{ @$serviceCategory->slug }}"]').addClass('bg--base text-white');
            $('#actionMenu a[href="{{ url()->current() }}"]').addClass('bg--base text-white');

        })(jQuery);
    </script>
@endpush