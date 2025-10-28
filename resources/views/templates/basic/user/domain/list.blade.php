@extends($activeTemplate.'layouts.master')

@section('content') 
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <table class="table table--responsive--md">
            <thead>
                <tr>
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
                            <a href="http://{{ @$domain->domain }}" target="_blank" class="fw-bold">
                                {{ @$domain->domain }}
                            </a>
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
                            <a href="{{ route('user.domain.details', $domain->id) }}" class="badge badge--icon badge--fill-base" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('View')">
                                <i class="fas fa-desktop"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <x-empty-message table={{ true }} />
                @endforelse
            </tbody>
        </table>
        @if($domains->hasPages())
            <div class="mt-5">
                {{ paginateLinks($domains) }}
            </div>
        @endif
    </div>
</div>
@endsection


