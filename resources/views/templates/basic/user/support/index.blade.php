@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light section-full"> 
    <div class="container">
        <table class="table table--responsive--md">
            <thead>
                <tr>
                    <th>@lang('Subject')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Priority')</th>
                    <th>@lang('Last Reply')</th>
                    <th>@lang('Action')</th>
                </tr> 
            </thead>
            <tbody>
                @forelse($supports as $key => $support)
                    <tr>
                        <td>
                            <div>
                                <a href="{{ route('ticket.view', $support->ticket) }}" class="fw-bold text--base">
                                    [@lang('Ticket')#{{ $support->ticket }}]
                                    <br>
                                </a>
                                <span class="d-block">{{ __($support->subject) }}</span>
                            </div>
                        </td>
                        <td>
                            @php echo $support->statusBadge; @endphp
                        </td>
                        <td>
                            @if($support->priority == 1)
                                <span class="badge badge--dark">@lang('Low')</span>
                            @elseif($support->priority == 2)
                                <span class="badge  badge--warning">@lang('Medium')</span>
                            @elseif($support->priority == 3)
                                <span class="badge badge--danger">@lang('High')</span>
                            @endif
                        </td>
                        <td>
                            <div>
                                {{ showDateTime($support->last_reply) }}<br>{{ diffForHumans($support->last_reply) }}
                            </div>
                        </td>
 
                        <td>
                            <a href="{{ route('ticket.view', $support->ticket) }}" class="badge badge--icon badge--fill-base" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('View')">
                                <i class="fas fa-desktop"></i>
                            </a> 
                        </td>
                    </tr>
                @empty 
                    <x-empty-message table={{ true }} />
                @endforelse
            </tbody>
        </table>
        @if($supports->hasPages())
            <div class="mt-5">
                {{ paginateLinks($supports) }}
            </div>
        @endif
    </div>
</div>
@endsection
