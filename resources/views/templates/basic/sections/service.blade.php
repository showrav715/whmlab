@php
    $serviceContent = @getContent('service.content', true);
    $categories = App\Models\ServiceCategory::active()->get();
@endphp

<div class="col-12 py-5 bg--light">
    <div class="container px-3">
        <!-- Jumbotron -->
        <div class="text-center">
            <h3>{{ __(@$serviceContent->data_values->heading) }}</h3>
            <p class="mb-5">{{ __(@$serviceContent->data_values->subheading) }}</p>
        </div>
        <!-- Jumbotron -->

        <div class="d-flex gap-4 flex-wrap justify-content-center">
            @foreach ($categories as $category)
                <div class="service-card">
                    <div class="card h-100">
                        <h5 class="card-header text-center bg-dark-two service-title">{{ __($category->name) }}</h5>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <p class="card-text mb-3">
                                {{ strLimit(__($category->short_description), 150) }}
                            </p>
                            <div class="text-center">
                                <a href="{{ route('service.category', $category->slug) }}" class="btn btn--base btn--sm">
                                    @lang('Browse Products')
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('style')
    <style>
        .service-title {
            font-size: 1rem;
        }

        .service-card {
            flex-basis: 380px;
        }

        @media(max-width: 1399px) {
            .service-card {
                flex-basis: 320px;
            }
        }

        @media(max-width: 1199px) {
            .service-card {
                flex-basis: 290px;
            }
        }

        @media(max-width: 991px) {
            .service-card {
                flex-basis: 210px;
            }

            .service-title {
                font-size: 0.88rem;
            }
        }

        @media(max-width: 767px) {
            .service-card {
                flex-basis: 240px;
            }

            .service-title {
                font-size: 0.88rem;
            }
        }

        @media(max-width: 540px) {
            .service-card {
                flex-basis: 220px;
            }

            .service-title {
                font-size: 0.88rem;
            }
        }

        @media(max-width: 500px) {
            .service-card {
                flex-basis: 280px;
            }
        }
    </style>
@endpush
