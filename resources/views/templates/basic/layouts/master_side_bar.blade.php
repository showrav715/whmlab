@extends($activeTemplate.'layouts.app')

@section('app')    

    @php
        $user = auth()->user();
    @endphp 

    @include($activeTemplate.'partials.auth_header')
    @include($activeTemplate.'partials.breadcrumb')
 
    <div class="col-12 service-category bg--light">
        <div class="container px-3">
            <div class="row gy-4 justify-content-center">
 
                <div class="row py-3 px-xl-0">

                    <div class="col-lg-3 mb-4"> 
                        <div class="collapable-sidebar">
                            <div class="collapable-sidebar__inner">
                                <button type="button" class="collapable-sidebar__close d-lg-none d-block"><i class="las la-times"></i> </button>
                    
                                <div class="card mb-4">
                                    <div class="card-header border-bottom p-2 bg-dark-two text-center fw-bold">@lang('My Information')</div>
                                    <div class="card-body">
                                        <strong>{{ __($user->fullname) }}</strong> 
                                        <span class="d-block mt-1"> 
                                            {{ __(@$user->address) }}
                                        </span>
                                        <span class="d-block mt-1">
                                            {{ @$user->city ? __(@$user->city).',' : null }} 
                                            {{ @$user->state ? __(@$user->state).',' :  null }}
                                            {{ @$user->zip ? @$user->zip.',' : null }}
                                        </span>
                                        <span class="mt-1">{{ __(@$user->country_name) }}</span>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('user.profile.setting') }}" class="btn btn--base border w-100"><i class="fas fa-pencil-alt me-1"></i> @lang('Update')</a>
                                    </div>
                                </div>
                             
                                <div class="card mb-4">
                                    <div class="card-header border-bottom p-2 bg-dark-two text-center fw-bold">@lang('Support')</div> 
                                    <div class="card-body">
                                        <a href="{{ route('ticket.open') }}" class="btn border w-100"><i class="fas fa-plus me-1"></i> @lang('New Ticket')</a>
                                    </div>
                                </div>
                    
                                <nav id="actionMenu" class="collapse d-block sidebar collapse bg-white border">
                                    <div class="position-sticky">
                                        <div class="list-group list-group-flush">
                                            <span class="border-bottom p-2 bg-dark-two text-center fw-bold">
                                                <span>@lang('Shortcuts')</span>
                                            </span>  
                                            @if(@$serviceCategories->first())
                                                <a href="{{ route('service.category', [@$serviceCategories->first()->slug, 'all=']) }}" class="list-group-item list-group-item-action py-2 ripple">
                                                    <span>@lang('Order New Service')</span> 
                                                </a>
                                            @endif
                                            <a href="{{ route('register.domain') }}" class="list-group-item list-group-item-action py-2 ripple">
                                                <span>@lang('Register New Domain')</span>
                                            </a>
                                            <a href="{{ route('user.logout') }}" class="list-group-item list-group-item-action py-2 ripple">
                                                <span>@lang('Logout')</span> 
                                            </a>
                                        </div>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div> 

                    <div class="row d-lg-none d-block">
                        <div class="col-12 ">
                            <div class="show-sidebar-bar dashboard-bar">
                                <i class="las la-bars"></i>
                            </div>
                        </div>
                    </div>

                    @yield('content')
                 </div>

            </div> 
        </div>   
    </div> 

    @include($activeTemplate.'partials.footer')
@endsection 
 


