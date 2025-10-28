@php
    
    if (request()->routeIs('home')) {
        $blogs = @getContent('blog.element', limit: 3);
    } else {
        $blogs = App\Models\Frontend::where('data_keys', 'blog.element')->orderBy('id', 'DESC')->paginate(getPaginate());
    }
    
@endphp

<div class="contact-section section-full pt-60 pb-60 bg--light">
    <div class="container">
        <div class="card custom--card">
            <div class="card-body">
                <div class="announcements">

                    @forelse($blogs as $blog)
                        <div class="announcement mb-4 border-bottom pb-4">
                            <h3>
                                <a href="{{ route('blog.details', [slug(@$blog->data_values->title)]) }}">
                                    {{ strLimit(__($blog->data_values->title), 80) }}
                                </a>
                            </h3>
                            <ul class="list-inline">
                                <li class="list-inline-item text-muted pr-3">
                                    <i class="far fa-calendar-alt fa-fw"></i>
                                    {{ showDateTime(@$blog->created_at, 'd F Y') }}
                                </li>
                            </ul>
                            <article class="mt-3">
                                @php echo strLimit(strip_tags($blog->data_values->description), 500) @endphp
                            </article>
                            <a href="{{ route('blog.details', [slug(@$blog->data_values->title)]) }}" class="btn btn--base btn--sm">
                                @lang('Continue reading') <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    @empty
                        <x-empty-message div="{{ true }}" />
                    @endforelse

                    @if (!request()->routeIs('home'))
                        {{ paginateLinks($blogs) }}
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
