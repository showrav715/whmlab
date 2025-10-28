@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 bg--transparent shadow-none">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two bg-white">
                        <thead>
                        <tr>
                            
                            <th>@lang('Name')</th>
                            <th>@lang('Group')</th>
                            <th>@lang('URL')</th>
                            <th>@lang('Username')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr> 
                        </thead>  
                        <tbody>
                            @forelse($servers as $server)
                                @php 
                                    $serverGroup = @$server->group;
                                    $serverType = @$serverGroup->getType
                                @endphp
                                <tr>  
                                    <td>
                                        <span class="fw-bold">{{ __($server->name) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap justify-content-xl-center justify-content-end align-items-center">
                                            <span class="me-1">{{ __(@$serverGroup->name) }}</span>
                                            (@php echo getProductModuleLogo(@$server->group->type); @endphp)
                                        </div>
                                    </td>
                                    <td>
                                        {{ $server->hostname }} 
                                    </td>

                                    <td>
                                        {{ __($server->username) }}
                                    </td>

                                    <td>
                                        @php echo $server->showStatus; @endphp
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline--primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="las la-ellipsis-v"></i>@lang('Action')
                                        </button>
                                        <div class="dropdown-menu">
                                            @permit('admin.server.login')
                                                <a href="{{ route('admin.server.login', $server->id) }}" class="dropdown-item" 
                                                    data-modal_title="@lang("Login to $serverType")"
                                                >
                                                    <i class="lab la-whmcs"></i> @lang("Login to $serverType")
                                                </a>
                                            @endpermit
                                            @permit('admin.server.edit.page')
                                                <a href="{{ route('admin.server.edit.page', $server->id) }}" class="dropdown-item"
                                                    data-modal_title="@lang('Edit')"
                                                >
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </a>
                                            @endpermit
                                            @permit('admin.server.status')
                                                @if($server->status == 0)
                                                    <a href="javascript:void(0)"
                                                            class="dropdown-item confirmationBtn"
                                                            data-action="{{ route('admin.server.status', $server->id) }}"
                                                            data-question="@lang('Are you sure to enable this server?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item confirmationBtn"
                                                    data-action="{{ route('admin.server.status', $server->id) }}"
                                                    data-question="@lang('Are you sure to disable this server?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @else 
                                                @if($server->status == 0)
                                                    <a href="javascript:void(0)" class="dropdown-item">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </a>
                                                @else
                                                    <a href="javascript:void(0)" class="dropdown-item">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </a>
                                                @endif
                                            @endpermit
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($servers->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($servers) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />

@endsection

@push('breadcrumb-plugins')
<div class="justify-content-end d-flex flex-wrap gap-2">
    @permit('admin.groups.server')
        <a class="btn btn-sm btn-outline--success" href="{{ route('admin.groups.server') }}">
            <i class="las la-plus"></i>@lang('Add Server Group')
        </a>
    @endpermit
    @permit('admin.server.add.page')
        <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.server.add.page') }}">
            <i class="las la-plus"></i>@lang('Add Server')
        </a>
    @endpermit
</div>
<a href="{{ session()->get('loginUrl') ?? '#' }}" class="loginUrl" target="_blank"></a>
@endpush 

@push('style')
<style>
.table-responsive {
    background: transparent;
    min-height: 350px;
}
.dropdown-toggle::after {
    display: inline-block;
    margin-left: 0.255em;
    vertical-align: 0.255em;
    content: "";
    border-top: 0.3em solid;
    border-right: 0.3em solid transparent;
    border-bottom: 0;
    border-left: 0.3em solid transparent;
}
</style>
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";

            var loginUrl = @json(session()->get('loginUrl'));

            if(loginUrl){
                document.querySelector('.loginUrl').click();
            }

        })(jQuery);
    </script>
@endpush 
