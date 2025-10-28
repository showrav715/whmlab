@extends($activeTemplate.'layouts.frontend')

@section('content')
<div class="policy-section pt-60 pb-60 bg--light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="policy-content">
                    @php
                        echo $policy->data_values->details
                    @endphp
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
