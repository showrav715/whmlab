@extends('admin.layouts.app')

@section('panel')
<form class="form-horizontal" method="post" action="{{ route('admin.server.update') }}">
    @csrf 
    <input type="hidden" name="id" required value="{{ $server->id }}">
    <div class="row">
        <div class="col-lg-6 form-group">
            <div class="card">
                <div class="card-header w-100 bg--dark">
                    <h5 class="text--white">@lang('Name and Hostname')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Select Group')</label>
                                <select name="server_group_id" class="form-control" required>
                                    <option value="" hidden>@lang('Select One')</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}" data-type="{{ $group->getType }}">
                                            {{ __($group->name) }} ({{ $group->getType }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>   
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Name')</label>
                                <input type="text" class="form-control" name="name" required value="{{ $server->name }}">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-2 col-lg-12 col-xxl-3">
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Protocol')</label>
                                    </div>
                                </div>
                                <select name="protocol" class="form-control" required>
                                    <option value="https://" {{ $server->protocol == 'https://' ? 'selected' : null }}>@lang('https')</option>
                                    <option value="http://" {{ $server->protocol == 'http://' ? 'selected' : null }}>@lang('http')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-8 col-xxl-6">
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Hostname')</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="host" required value="{{ $server->host }}" placeholder="abc.server.com">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-4 col-xxl-3"> 
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Port')</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="port" required value="{{ $server->port }}" placeholder="2087">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ip_address" value="{{ $server->ip_address }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="card">
                    <div class="card-header bg--dark">
                        <h5 class="text--white">@lang('Server Details')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input type="text" class="form-control" name="username" required value="{{ $server->username }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input type="password" class="form-control" name="password" value="{{ $server->password }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 cpanel-input">
                                <div class="form-group">
                                    <label>@lang('API Token')</label>
                                    <input type="text" class="form-control" name="api_token" value="{{ $server->api_token }}">
                                </div>
                            </div>
                            <div class="col-lg-6 cpanel-input">
                                <div class="form-group">
                                    <label>@lang('Security Token')</label>
                                    <input type="text" class="form-control" name="security_token" value="{{ $server->security_token }}" placeholder="123456789">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 form-group">
            <div class="card h-100">
                <div class="card-header bg--dark">
                    <h5 class="text--white">@lang('Nameservers')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Primary Nameserver')</label>
                                <input type="text" class="form-control" name="ns1" value="{{ $server->ns1 }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns1_ip" value="{{ $server->ns1_ip }}" required>
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Secondary Nameserver')</label>
                                <input type="text" class="form-control" name="ns2" value="{{ $server->ns2 }}" required>
                            </div> 
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns2_ip" value="{{ $server->ns2_ip }}" required>
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Third Nameserver')</label>
                                <input type="text" class="form-control" name="ns3" value="{{ $server->ns3 }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns3_ip" value="{{ $server->ns3_ip }}">
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Fourth Nameserver')</label>
                                <input type="text" class="form-control" name="ns4" value="{{ $server->ns4 }}">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns4_ip" value="{{ $server->ns4_ip }}">
                            </div>
                        </div>
    
                    </div>
                </div>
            </div>
        </div>
    
        @permit('admin.server.update')
            <div class="col-lg-12 mt-3">
                <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
            </div>
        @endpermit
    </div>
    </form>
@endsection

@permit('admin.servers')
    @push('breadcrumb-plugins') 
        <a href="{{ route('admin.servers') }}" class="btn btn-sm btn-outline--primary">
            <i class="la la-undo"></i> @lang('Go to Servers')
        </a>
    @endpush
@endpermit

@push('script')
    <script>
        (function($){
            "use strict"; 

            var group = '{{ $server->server_group_id }}'; 
          
            if(group){
                $('select[name=server_group_id]').val(group);
            }

            $('select[name=server_group_id]').on('change', function() {
                var type = $(this).find('option:selected').data('type');
                
                if(type == 'Cpanel'){
                    $('.cpanel-input').removeClass('d-none');
                }else{
                    $('.cpanel-input').addClass('d-none');
                }
            }).change();

        })(jQuery);    
    </script> 
@endpush
