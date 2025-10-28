@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <div class="contact-section pt-60 pb-60 bg--light section-full">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body extra-padding">
                    <h3>{{ __(@$blog->data_values->title) }}</h3>
                    <ul class="list-inline mt-2">
                        <li class="list-inline-item text-muted pr-3">
                            <i class="far fa-calendar-alt fa-fw"></i>
                            {{ showDateTime(@$blog->created_at, 'l, d F, Y') }} 
                        </li>
                        <li class="list-inline-item text-muted pr-3">
                            <i class="far fa-clock fa-fw"></i>
                            {{ showDateTime(@$blog->created_at, 'h:i a') }}
                        </li>
                    </ul>
                    <div class="mt-4 mb-4">
                        @php echo @$blog->data_values->description; @endphp
                    </div>

                    <div class="fb-comments mb-2" data-href="{{ route('blog.details',[slug(@$blog->data_values->title)]) }}" data-numposts="5">
                    </div>

                    <a href="{{ route('blogs') }}" class="btn btn--base btn--sm">
                        <i class="fas fa-angle-double-left"></i> @lang('Back')
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
