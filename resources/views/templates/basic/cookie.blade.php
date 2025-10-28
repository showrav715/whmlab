@extends($activeTemplate.'layouts.frontend')

@section('content')
<div class="pt-60 pb-60 bg--light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="policy-content">
                    @php
                        echo $cookie->data_values->description
                    @endphp
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
