@extends($activeTemplate.'layouts.'.$layout)

@section('content')
<div class="pt-60 pb-60 bg--light">
    <div class="container"> 
        <div class="row justify-content-center"> 
            <div class="col-md-10"> 
                <div class="card custom--card style-two">
                    <div class="card-header card-header-bg d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="button-title-badge d-flex flex-wrap align-items-center gap-2">
                            <div class="button-badge d-flex flex-wrap align-items-center justify-content-between">
                                @php echo $myTicket->statusBadge; @endphp
                                <div class="d-block d-sm-none">
                                    @if ($myTicket->status != 3 && $myTicket->user)
                                        <button class="btn btn--danger close-button btn--sm confirmationBtn" type="button"
                                            data-question="@lang('Are you sure to close this ticket?')" data-action="{{ route('ticket.close', $myTicket->id) }}"><i
                                                class="las la-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <h5 class="card-title mt-0 mb-0">
                                [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                            </h5>
                        </div>
                        <div class="d-sm-block d-none">
                            @if ($myTicket->status != 3 && $myTicket->user)
                                <button class="btn btn--danger close-button btn--sm confirmationBtn" type="button"
                                    data-question="@lang('Are you sure to close this ticket?')" data-action="{{ route('ticket.close', $myTicket->id) }}"><i
                                        class="las la-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row justify-content-between">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea name="message" class="form-control form--control" rows="4">{{ old('message') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn--base btn--sm addAttachment btn--success mt-4">
                                    <i class="fa fa-plus"></i> @lang('Add New')
                                </button>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Attachments')</label> 
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
                            <button type="submit" class="btn btn--base w-100 mt-4">@lang('Submit')</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        @foreach($messages as $message)
                            @if($message->admin_id == 0)
                                <div class="row border border-radius-3 my-3 py-3 mx-2">
                                    <div class="col-md-3 border-end text-end">
                                        <h5 class="my-3">{{ $message->ticket->fullname }}</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted fw-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>{{$message->message}}</p>
                                        @if($message->attachments->count() > 0)
                                            <div class="mt-2">
                                                @foreach($message->attachments as $k=> $image)
                                                    <a href="{{route('ticket.download',encrypt($image->id))}}" class="me-3"><i class="fa fa-file"></i>  @lang('Attachment') {{++$k}} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                            @else
                                <div class="row border border-warning border-radius-3 my-3 py-3 mx-2" style="background-color: #ffd96729">
                                    <div class="col-md-3 border-end text-end">
                                        <h5 class="my-3">{{ $message->admin->name }}</h5>
                                        <p class="lead text-muted">@lang('Staff')</p>
                                    </div>
                                    <div class="col-md-9">
                                        <p class="text-muted fw-bold my-3">
                                            @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                        <p>{{$message->message}}</p>
                                        @if($message->attachments->count() > 0)
                                            <div class="mt-2">
                                                @foreach($message->attachments as $k=> $image)
                                                    <a href="{{route('ticket.download',encrypt($image->id))}}" class="me-3"><i class="fa fa-file"></i>  @lang('Attachment') {{++$k}} </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div> 

            </div>
        </div>
    </div>
</div>
 
<x-confirmation-modal basic={{ true }} /> 
@endsection

@push('style')
    <style>
        .input-group-text:focus{
            box-shadow: none !important;
        }
        input::file-selector-button{
            height: 45px;
        }
        @media (max-width: 575px) {
            .button-badge {
                   width: 100%;  
            }
        }
        @media (max-width: 575px) {
            .button-title-badge {
                   width: 100%;  
            }
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
