@extends($activeTemplate.'layouts.master')

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
                        <form action="{{route('user.kyc.submit')}}" method="post" enctype="multipart/form-data">
                            @csrf
             
                            <x-viser-form identifier="act" identifierValue="kyc" />
            
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div> 
            </div>
            
        </div>
    </div>   
</div> 
@endsection
