@extends($activeTemplate .'layouts.app')

@section('app')
    <div class="section maintenance-section bg--light section-full">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-md-8 col-12 text-center">
                    <div class="ban-section">
                        <h4 class="text-center text--base mb-4">
                            {{ __(@$maintenance->data_values->heading) }}
                        </h4>
                        <img src="{{ getImage(getFilePath('maintenance') . '/' . @$maintenance->data_values->image, getFileSize('maintenance')) }}" alt="@lang('Maintenance Image')">
                        <div class="mt-4">
                            @php
                                echo $maintenance->data_values->description
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    
<style>
    .maintenance-section {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    </style>
@endpush