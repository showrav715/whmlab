@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-8 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group position-relative mb-0">
                                <div class="system-search-icon"><i class="las la-search"></i></div>
                                <input class="form-control searchInput" type="search" placeholder="@lang('Search')...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-4">
                <div class="col-12">
                    <div class="emptyArea"></div>
                </div>
                @foreach ($settings as $key => $setting)
                    @php
                        $params = null;
                        if (@$setting->params) {
                            foreach ($setting->params as $paramVal) {
                                $params[] = array_values((array) $paramVal)[0];
                            }
                        }

                    @endphp
                    @permit($setting->route_name)
                    <div class="col-xxl-6 col-md-6 {{ $key }} searchItems">
                        @php
                            $params = null;
                            if (@$setting->params) {
                                foreach ($setting->params as $paramVal) {
                                    $params[] = array_values((array)$paramVal)[0];
                                }
                            }
                        @endphp
                        <x-widget style="2" link="{{ route($setting->route_name,$params) }}" icon="{{ $setting->icon }}" heading="{{ $setting->title }}" subheading="{{ $setting->subtitle }}" cover_cursor=1 icon_style="fill" color="primary" />
                    </div>
                    @endpermit
                @endforeach
            </div>
        </div>

        <div class="col-lg-4 col-md-12 mb-30">
            <div class="card bg--dark setupWrapper">
                <div class="card-header d-flex justify-content-between flex-wrap align-items-center">
                    <h5 class="text--white">@lang('Setup')</h5>
                    <small>{{ array_sum($completed) }} @lang('of') <span class="totalCompletedSetup text--white"></span> @lang('Completed') </small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <ul class="ul-border setup">
                                <li class="text-dot">
                                    <i class="las la-{{ $completed['name_and_logo'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.setting.general') ? route('admin.setting.general') : 'javascript:void(0)' }}">@lang('Set company name') </a> @lang('and')
                                    <a href="{{ permit('admin.setting.logo.icon') ? route('admin.setting.logo.icon') : 'javascript:void(0)' }}">@lang('logo')</a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['cron'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="javascript:void(0)" class="cronModalBtn">@lang('Setup cron automation tasks') </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['domain_setup'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.tld') ? route('admin.tld') : 'javascript:void(0)' }}">
                                        @lang('Manage domain/TLD setup')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['domain_register'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.register.domain') ? route('admin.register.domain') : 'javascript:void(0)' }}">
                                        @lang('Activate your first domain register')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['configurable_group'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.configurable.groups') ? route('admin.configurable.groups') : 'javascript:void(0)' }}">
                                        @lang('Set configurable group')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['product'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.products') ? route('admin.products') : 'javascript:void(0)' }}">
                                        @lang('Create your first product')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['setup_gateway'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.gateway.automatic.index') ? route('admin.gateway.automatic.index') : 'javascript:void(0)' }}">
                                        @lang('Activate/add your first payment gateway')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['service_category'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.service.category') ? route('admin.service.category') : 'javascript:void(0)' }}">
                                        @lang('Create first service category')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['server_group'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.groups.server') ? route('admin.groups.server') : 'javascript:void(0)' }}">
                                        @lang('Create server group')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['server'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.servers') ? route('admin.servers') : 'javascript:void(0)' }}">
                                        @lang('Setup at least one server')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['billing_setting'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.billing.setting') ? route('admin.billing.setting') : 'javascript:void(0)' }}">
                                        @lang('Setup invoice generation days')
                                    </a>
                                </li>
                                <li class="mt-2 text-dot">
                                    <i class="las la-{{ @$completed['defaultDomainRegister'] ? 'check text--success' : 'times text--danger' }}"></i>
                                    <a href="{{ permit('admin.register.domain') ? route('admin.register.domain') : 'javascript:void(0)' }}">
                                        @lang('Setup default domain register for domain availability')
                                    </a>
                                </li>
                                @if(isSuperAdmin())
                                    <li class="mt-2 text-dot">
                                        <i class="las la-{{ @$completed['admin_profile_setup'] ? 'check text--success' : 'times text--danger' }}"></i>
                                        <a href="{{ route('admin.profile') }}">@lang('Setup profile information for Namecheap') </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('admin.partials.cron_modal')
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/highlighter22.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.cronModalBtn').on('click', function(){
                $('#cronModal').modal('show');
            });

            var settingsData = @json($settings);
            // Function to filter settings based on search query
            function filterSettings(query) {
                let filteredSettings = [];
                for (var key in settingsData) {
                    if (settingsData.hasOwnProperty(key)) {
                        var setting = settingsData[key];
                        // Check if the query matches keyword, title, or subtitle
                        var keywordMatch = setting.keyword.some(function(keyword) {
                            return keyword.toLowerCase().includes(query.toLowerCase());
                        });
                        var titleMatch = setting.title.toLowerCase().includes(query.toLowerCase());
                        var subtitleMatch = setting.subtitle.toLowerCase().includes(query.toLowerCase());

                        // If any match is found, add the setting to filtered settings
                        if (keywordMatch || titleMatch || subtitleMatch) {
                            filteredSettings[key] = setting;
                        }
                    }
                }
                return filteredSettings;
            }

            function isEmpty(obj) {
                return Object.keys(obj).length === 0;
            }

            // Function to render filtered settings
            function renderSettings(filteredSettings, query) {
                $('.searchItems').addClass('d-none');
                $('.emptyArea').html('');
                if (isEmpty(filteredSettings)) {
                    $('.emptyArea').html(`<div class="col-12 searchItems text-center mt-4"><div class="card">
                                <div class="card-body">
                                    <div class="empty-search text-center">
                                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                                        <h5 class="text-muted">@lang('No search result found.')</h5>
                                    </div>
                                </div>
                            </div>
                        </div>`);
                } else {
                    for (const key in filteredSettings) {
                        if (Object.hasOwnProperty.call(filteredSettings, key)) {
                            const element = filteredSettings[key];
                            var setting = element;
                            $(`.searchItems.${key}`).removeClass('d-none');
                        }
                    }
                }
            }


            $('.searchInput').on('input', function() {
                var query = $(this).val().trim();
                var filteredData = filterSettings(query);
                renderSettings(filteredData, query);
            });

            $('.searchInput').highlighter22({
                targets: [".widget-two__content h3", ".widget-two__content p"],
            });

            $('.searchInput').focus();

            $('.totalCompletedSetup').text($('.setup li').length);
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .system-search-icon {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            aspect-ratio: 1;
            padding: 5px;
            display: grid;
            place-items: center;
            color: #888;
        }

        .system-search-icon~.form-control {
            padding-left: 45px;
        }

        .widget-seven .widget-seven__content-amount {
            font-size: 22px;
        }

        .widget-seven .widget-seven__content-subheading {
            font-weight: normal;
        }

        .empty-search img {
            width: 120px;
            margin-bottom: 15px;
        }

        a.item-link:focus,
        a.item-link:hover {
            background: #4634ff38;
        }

        .setupWrapper{
            position: sticky;
            top: 40px;
        }
        .ul-border li, .ul-border li a{
            color: #ffffff;
        }
        .ul-border li a:hover{
            color: #ffffff;
            text-decoration: underline;
        }
        .ul-border li:not(:last-child){
            border-bottom: 1px dotted #ffffff4a;
            padding-bottom: 30px;
        }
        .text-dot {
            text-overflow: ellipsis;
            overflow: hidden;
            width: 100%;
            height: 1.2em;
            white-space: nowrap;
        }
    </style>
@endpush
