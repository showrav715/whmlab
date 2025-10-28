@extends('admin.layouts.app')

@section('panel')
<form class="form-horizontal server-form" method="post" action="{{ route('admin.server.add') }}">
    @csrf 
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
                                <input type="text" class="form-control" name="name" required value="{{old('name')}}">
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-2 col-lg-12 col-xxl-3">
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Protocol')</label>
                                    </div>
                                </div>
                                <select name="protocol" class="form-control">
                                    <option value="https://" {{ old('protocol') == 'https://' ? 'selected' : null }}>@lang('https')</option>
                                    <option value="http://" {{ old('protocol') == 'http://' ? 'selected' : null }}>@lang('http')</option>
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
                                <input type="text" class="form-control" name="host" required value="{{old('host')}}" placeholder="abc.server.com">
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-4 col-xxl-3"> 
                            <div class="form-group">
                                <div class="justify-content-between d-flex flex-wrap">
                                    <div>
                                        <label>@lang('Port')</label>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="port" required value="{{old('port')}}" placeholder="2087">
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
                                    <input type="text" class="form-control" name="username" required value="{{old('username')}}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="col-lg-6 cpanel-input">
                                <div class="form-group">
                                    <label>@lang('API Token')</label>
                                    <input type="text" class="form-control" name="api_token" value="{{ old('api_token') }}" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6 cpanel-input">
                                <div class="form-group">
                                    <label>@lang('Security Token')</label>
                                    <input type="text" class="form-control" name="security_token" value="{{ old('security_token') }}" disabled placeholder="123456789">
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="justify-content-between d-flex">
                                        <label>@lang('Test')</label>
                                        <div class="connection d-none">
                                            <i>
                                                <i class="icon"></i>
                                                <small class="response">@lang('Attempting to connect to server')...</small>
                                            </i>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn--success h-45 w-100 testConnection">@lang('Test Connection') <i class="las la-angle-double-right"></i></button>
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
                                <input type="text" class="form-control" name="ns1" value="{{ old('ns1') }}" required disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns1_ip" value="{{ old('ns1_ip') }}" required disabled>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Secondary Nameserver')</label>
                                <input type="text" class="form-control" name="ns2" value="{{ old('ns2') }}" required disabled> 
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns2_ip" value="{{ old('ns2_ip') }}" required disabled>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Third Nameserver')</label>
                                <input type="text" class="form-control" name="ns3" value="{{ old('ns3') }}" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns3_ip" value="{{ old('ns3_ip') }}" disabled>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('Fourth Nameserver')</label>
                                <input type="text" class="form-control" name="ns4" value="{{ old('ns4') }}" disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>@lang('IP Address')</label>
                                <input type="text" class="form-control" name="ns4_ip" value="{{ old('ns4_ip') }}" disabled>
                            </div>
                        </div>

                    </div> 
                </div>
            </div>
        </div>
        
        @permit('admin.server.add')
            <div class="col-lg-12 mt-3">
                <button type="submit" class="btn btn--primary h-45 w-100" disabled>@lang('Submit')</button>
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

            var oldGroup = '{{ old("server_group_id") }}'; 
          
            if(oldGroup){
                $('select[name=server_group_id]').val(oldGroup);
            }

            $('select[name=server_group_id]').on('change', function() {
                var type = $(this).find('option:selected').data('type');
                
                if(type == 'Cpanel'){
                    $('.cpanel-input').removeClass('d-none');
                }else{
                    $('.cpanel-input').addClass('d-none');
                }
            }).change();

            $('.testConnection').on('click', function(){
                @permit('admin.server.test.connection')
                    $.ajax({ 
                        type:'POST',
                        url:'{{ route("admin.server.test.connection") }}',
                        data: $('.server-form').serialize(),
                        beforeSend: function() {
                            $('.connection').removeClass('d-none');
                            $('.connection .icon').html('<i class="fas fa-spinner fa-spin"></i>');
                            $('.connection .respone').text('Attempting to connect to server...');
                        },

                        success:function(response){
                            setTimeout(function() {
                                if(response.success){
                                    $('.connection').addClass('d-none');
                                    $("*[disabled]").not(true).removeAttr("disabled");
                                }
                                else if(response.error){
                                    $.each(response.error, function(key, value) {
                                        notify('error', value);
                                    });
                                    $('.connection').addClass('d-none');
                                }
                                else{
                                    notify('error', response.message);
                                    $('.connection .icon').html('<i class="fas fa-times"></i>');
                                }
                            }, 200);
                        }
                    });
                @endpermit
            });
        })(jQuery);
    </script> 
@endpush
