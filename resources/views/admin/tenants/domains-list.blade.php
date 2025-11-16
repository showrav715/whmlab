<div class="table-responsive">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>@lang('Domain')</th>
                <th>@lang('Type')</th>
                <th>@lang('Status')</th>
                <th>@lang('Action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($domains as $domain)
                <tr>
                    <td>
                        <div>
                            <strong>{{ $domain->domain }}</strong>
                            @if($domain->is_primary)
                                <span class="badge badge--success ms-1">@lang('Primary')</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge badge--{{ $domain->type == 'subdomain' ? 'primary' : 'info' }}">
                            {{ ucfirst($domain->type) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ $domain->getUrl() }}" target="_blank" class="text--primary">
                            <i class="fas fa-external-link-alt"></i> @lang('Visit')
                        </a>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @if(!$domain->is_primary)
                                <button type="button" class="btn btn-sm btn-outline--success setPrimary" 
                                    data-domain-id="{{ $domain->id }}" 
                                    data-tenant-id="{{ $tenantId }}">
                                    <i class="fas fa-star"></i>
                                </button>
                            @endif
                            
                            @if(!$domain->is_primary)
                                <button type="button" class="btn btn-sm btn-outline--danger deleteDomain" 
                                    data-domain-id="{{ $domain->id }}" 
                                    data-tenant-id="{{ $tenantId }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">@lang('No domains found')</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>