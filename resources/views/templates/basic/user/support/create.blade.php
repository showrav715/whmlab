@extends($activeTemplate.'layouts.master')

@section('content')
<div class="pt-60 pb-60 bg--light">
    <div class="container"> 
        <div class="row gy-4 justify-content-center">
            <div class="col-md-10">
                <div class="card custom--card style-two">
                    <div class="card-header">
                        <h6 class="card-title text-center">{{ __($pageTitle) }}</h6>
                    </div>
                    <div class="card-body"> 
                        <form  action="{{route('ticket.store')}}"  method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Subject')</label>
                                        <input type="text" name="subject" value="{{old('subject')}}" class="form-control form--control h-45" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Priority')</label>
                                        <select name="priority" class="form-control form--control h-45 form-select" required>
                                            <option value="3">@lang('High')</option>
                                            <option value="2">@lang('Medium')</option>
                                            <option value="1">@lang('Low')</option>
                                        </select>
                                    </div>
                                </div> 
                                <div class="col-12 form-group mt-2">
                                    <label class="form-label">@lang('Message')</label>
                                    <textarea name="message" id="inputMessage" rows="6" class="form-control form--control" required>{{old('message')}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="text-end">
                                    <button type="button" class="btn btn--base btn--sm addAttachment btn--success mt-4">
                                        <i class="fa fa-plus"></i> @lang('Add New')
                                    </button>
                                </div> 
                                <div class="file-upload">
                                    <div class="d-flex flex-wrap gap-1">
                                        <div>
                                            <small class="text-danger">@lang('Max 5 files can be uploaded'). @lang('Maximum upload size is') {{ ini_get('upload_max_filesize') }}</small>
                                        </div>
                                        <div>
                                            <span class="ticket-attachments-message text-muted">
                                                (@lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'), .@lang('pdf'), .@lang('doc'), .@lang('docx'))
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row g-4 fileUploadsContainer mt-1"></div>
                                </div>
                            </div>
                            <div class="form-group mt-4">
                                <button class="btn btn--base w-100">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>
        .input-group-text:focus{
            box-shadow: none !important;
        } 
        input::file-selector-button{
            height: 45px;
        }
    </style>
@endpush 

@push('script')
    <script>
        (function ($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click',function(){
                if (fileAdded >= 5) {
                    notify('error','You\'ve added maximum number of file');
                    return $(this).attr('disabled',true);
                }
                fileAdded++;  
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control h-45" required />
                                <button type="button" class="input-group-text btn--danger removeFile"><i class="las la-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            }); 
            $(document).on('click','.removeFile',function(){
                $('.addAttachment').removeAttr('disabled',true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);

    </script>
@endpush
