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
                            <th>@lang('User')</th>
                            <th>@lang('Domain')</th>
                            <th>@lang('Registration Date')</th>
                            <th>@lang('Next Due Date')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>  
                        </thead> 
                        <tbody> 
                            @forelse($domains as $domain)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{$domain->user->fullname}}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ permit('admin.users.detail') ? route('admin.users.detail', $domain->user_id) : 'javascript:void(0)' }}">
                                                <span>@</span>{{ $domain->user->username }}
                                            </a>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="http://{{ @$domain->domain }}" target="_blank" class="fw-bold">{{ @$domain->domain }}</a>
                                    </td>  
                                    <td>
                                        {{ showDateTime(@$domain->reg_date, 'd/m/Y') }}
                                    </td> 
                                    <td>
                                        {{ showDateTime(@$domain->next_due_date, 'd/m/Y') }}
                                    </td>
                                    <td>
                                        @php echo @$domain->showStatus; @endphp
                                    </td> 
                                    <td>
                                        @permit('admin.order.domain.details')
                                            <a href="{{ route('admin.order.domain.details', $domain->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="las la-desktop text--shadow"></i> @lang('Details')
                                            </a>
                                        @else 
                                            <button class="btn btn-sm btn-outline--primary" disabled>
                                                <i class="las la-desktop text--shadow"></i> @lang('Details')
                                            </button>
                                        @endpermit
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
            @if ($domains->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($domains) }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@if(request()->routeIs('admin.domains'))
    @push('breadcrumb-plugins')
        <x-search-form placeholder="Username / Email / Domain" />
    @endpush
@endif