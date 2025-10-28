@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead> 
                        <tr>
                            <th>@lang('Service Provider')</th>
                            <th>@lang('Test Mode')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Default')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>  
                            @forelse($domainRegisters as $data)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $data->name }}</span>
                                    </td>
                                    <td>
                                        @php echo $data->showTestMode; @endphp
                                    </td>
                                    <td>
                                        @php echo $data->showStatus; @endphp
                                    </td>
                                    <td>
                                        @php echo $data->showDefault; @endphp
                                    </td>
                                    <td>
                                        <div class="button--group">
                                            @permit('admin.register.domain.update')
                                                <button class="btn btn-sm btn-outline--primary configBtn" data-data="{{ $data }}"> 
                                                    <i class="las la-cogs"></i> @lang('Config')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--primary" disabled> 
                                                    <i class="las la-cogs"></i> @lang('Config')
                                                </button>
                                            @endpermit
                                            
                                            @permit('admin.register.domain.status')
                                                @if($data->status == 0)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline--success confirmationBtn"
                                                            data-action="{{ route('admin.register.domain.status', $data->id) }}"
                                                            data-question="@lang('Are you sure to enable this domain register?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.register.domain.status', $data->id) }}"
                                                    data-question="@lang('Are you sure to disable this domain register?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            @else
                                                @if($data->status == 0)
                                                    <button type="button" class="btn btn-sm btn-outline--success" disabled>
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger" disabled>
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
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
            @if ($domainRegisters->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($domainRegisters) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />

{{-- CONFIG --}}
<div class="modal fade" id="configModal" tabindex="-1" role="dialog" aria-labelledby="autoRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Update Configuration'): <span class="provider-name"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.register.domain.update') }}">
                @csrf 
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="row">
                        <div class="configFields w-100"></div> 
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="checkbox" name="test_mode" id="test_mode">
                                <label for="test_mode">@lang('Test Mode')</label>
                            </div>
                        </div>

                        <div class="border-line-area style-two">
                            <h5 class="border-line-title">@lang('Nameservers')</h5>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_mode">@lang('Default Nameserver 1')</label>
                                <input class="form-control form-control-lg" type="text" name="ns1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_mode">@lang('Default Nameserver 2')</label>
                                <input class="form-control form-control-lg" type="text" name="ns2" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_mode">@lang('Default Nameserver 3')</label>
                                <input class="form-control form-control-lg" type="text" name="ns3">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_mode">@lang('Default Nameserver 4')</label>
                                <input class="form-control form-control-lg" type="text" name="ns4">
                            </div>
                        </div>
                        
                        <div class="col-md-12"> 
                            <div class="form-group">
                                <label>@lang('Default Domain Register')</label>
                                <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Default')" data-off="@lang('Unset')" name="default">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div id="autoRegisterModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button> 
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.register.domain.auto') }}">
                @csrf 
                <div class="modal-body"> 
                    @if(gs('auto_domain_register'))
                        <p class="question">@lang('Are you sure to disable auto domain register')</p>
                    @else 
                        <p class="question">@lang('Are you sure to enable auto domain register')</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@permit('admin.register.domain.auto')
    @push('breadcrumb-plugins')
        @if(gs('auto_domain_register'))
            <button class="btn btn-sm btn-outline--danger autoRegister">
                <i class="las la-ban"></i>@lang('Disable Auto Domain Register')
            </button>
        @else 
            <button class="btn btn-sm btn-outline--primary autoRegister">
                <i class="las la-check"></i>@lang('Enable Auto Domain Register')
            </button>
        @endif
    @endpush
@endpermit

@push('script')
    <script>
        (function($){
            "use strict";  
 
            $('.autoRegister').on('click', function () {
                var modal = $('#autoRegisterModal');
                modal.modal('show');
            });

            $('.configBtn').on('click', function () {
                var modal = $('#configModal'); 
                var data = $(this).data('data');
                var appendArea = modal.find('.configFields');
                appendArea.empty();

                $('input[name=ns1]').val(data.ns1);
                $('input[name=ns2]').val(data.ns2);
                $('input[name=ns3]').val(data.ns3);
                $('input[name=ns4]').val(data.ns4);

                modal.find('.provider-name').text(data.name);
                modal.find('input[name=id]').val(data.id);

                if(data.test_mode == 1){
                    modal.find('input[name=test_mode]').prop('checked', true);
                }else{
                    modal.find('input[name=test_mode]').prop('checked', false);
                }

                if(data.default == 1){
                    modal.find('input[name=default]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=default]').bootstrapToggle('off');
                }

                for(var [key, item] of Object.entries(data.params)){
                    appendArea.append(`
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class='capitalize'>${item.title}</label>
                                <input type='text' class='form-control' placeholder='${key}' value='${item.value}' name='${key}'>
                            </div>
                        </div>
                    `);
                }

                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush





