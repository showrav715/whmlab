@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="col-12 bg--light pt-60 pb-60">
    <div class="container px-3">
        <div class="row gy-4 justify-content-center">
       
            <div class="col-lg-8">
                <div class="card custom--card">
                    <div class="card-header">
                        <h6 class="card-title text-center">@lang('KYC Form')</h6>
                    </div>
                    <div class="card-body">
                        @if ($user->kyc_data)
                            <ul class="list-group list-group-flush">  
                                @foreach ($user->kyc_data as $val)
                                    @continue(!$val->value)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ __(@$val->name) }}</span> 
                                        <span>
                                            @if ($val->type == 'checkbox')
                                                {{ implode(',', $val->value) }}
                                            @elseif($val->type == 'file')
                                                <a href="{{ route('user.download.attachment',encrypt(getFilePath('verify').'/'.$val->value)) }}"><i class="fa fa-file"></i> @lang('Attachment') </a>
                                            @else
                                                <p>{{ __($val->value) }}</p>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <x-empty-message message="KYC data not found" div={{ true }} />
                        @endif
                    </div>
                </div> 
            </div>
            
        </div>
    </div>   
</div> 
@endsection
