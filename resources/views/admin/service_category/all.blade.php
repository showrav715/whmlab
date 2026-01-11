@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('Service Category')</th>
                            <th>@lang('Slug')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ __($category->name) }}</span>
                                    </td>

                                    <td>
                                        <span>{{ $category->slug }}</span>
                                    </td>

                                    <td> 
                                       @php echo $category->showStatus; @endphp
                                    </td>

                                    <td>
                                        <div class="button--group">
                                            @permit('admin.service.category.update')
                                                <button class="btn btn-sm btn-outline--primary editBtn" data-data="{{ $category }}">
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline--primary" disabled>
                                                    <i class="la la-pencil"></i> @lang('Edit')
                                                </button>
                                            @endpermit

                                            @permit('admin.service.category.status')
                                                @if($category->status == 0)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline--success confirmationBtn"
                                                            data-action="{{ route('admin.service.category.status', $category->id) }}"
                                                            data-question="@lang('Are you sure to enable this service category?')">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.service.category.status', $category->id) }}"
                                                    data-question="@lang('Are you sure to disable this service category?')">
                                                            <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            @else 
                                                @if($category->status == 0)
                                                    <button type="button" class="btn btn-sm btn-outline--success" disabled>
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-outline--danger" disabled>
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            @endpermit
                                        </div>
                                    </td> 
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($categories->hasPages())
                <div class="card-footer py-4">
                    {{ paginateLinks($categories) }}
                </div>
            @endif
        </div>
    </div>
</div>

<x-confirmation-modal />

{{-- NEW MODAL --}}
<div id="createModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('New Service Category')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.service.category.add') }}">
                @csrf 
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control add_name" name="name" required value="{{old('name')}}" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <div class="justify-content-between d-flex">
                            <label>@lang('Slug')</label>
                            <div class="addSlugValidatation d-none">
                               <i>
                                    <span class="addSlugIcon"></span>
                                    <small class="addAjaxResponse">@lang('Validating')...</small>
                               </i>
                            </div>
                        </div>
                        <input type="text" class="form-control add_slug" name="slug" required value="{{old('slug')}}" oninput="this.value = this.value.replace(/[^a-z0-9\s -]/gi, '')">
                        @permit('admin.check.slug')
                            <code class="addUrl">{{ route('home') }}/store/<span class="text--primary"></span></code>
                        @endpermit
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="short_description" class="form-control" rows="4" required>{{old('short_description')}}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}} 
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">@lang('Update Service Category')</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.service.category.update') }}">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control edit_name" name="name" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <div class="justify-content-between d-flex">
                            <label>@lang('Slug')</label>
                            <div class="editSlugValidatation d-none">
                               <i>
                                    <span class="editSlugIcon"></span>
                                    <small class="editAjaxResponse">@lang('Validating')...</small>
                               </i>
                            </div>
                        </div>
                        <input type="text" class="form-control edit_slug" name="slug" required oninput="this.value = this.value.replace(/[^a-z0-9\s -]/gi, '')">
                        @permit('admin.check.slug')
                            <code class="editUrl">{{ route('home') }}/store/<span class="text--primary"></span></code>
                        @endpermit
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="short_description" class="form-control" required rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@permit('admin.service.category.add')
    @push('breadcrumb-plugins')
        <button class="btn btn-sm btn-outline--primary addBtn">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endpush
@endpermit

@push('script')
    <script>
        (function($){
            "use strict";  

            var slugRule = /^[0-9a-zA-Z -]+$/;

            $('.addBtn').on('click', function () {
                var modal = $('#createModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                var record = $(this).data('data');
                $('.editSlugValidatation').addClass('d-none').removeClass('d-inline');   

                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=name]').val(record.name);
                modal.find('input[name=slug]').val(record.slug);
                modal.find('.editUrl span').text(record.slug);
                modal.find('textarea[name=short_description]').val(record.short_description);

                modal.modal('show');
            });
 
            function makeSlug(input){
                return input.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
            }

            $('.add_name').on('input', function(){
                var input = $(this).val();
                var slug = makeSlug(input);
        
                if(input.match(slugRule)){
                    $('.add_slug').val(slug);
                    return checkSlug(input, slug, 'add');
                }

                $('.addSlugValidatation').addClass('d-none').removeClass('d-inline');
            });

            $('.add_slug').on('input', function(){ 
                var input = $(this).val();
                var slug = makeSlug(input);

                if(input.match(slugRule)){
                    $(this).val(slug);
                    return checkSlug(input, slug, 'add');
                }

                $('.addSlugValidatation').addClass('d-none').removeClass('d-inline');
            });

            $('.edit_name').on('input', function(){
            
                var input = $(this).val();
                var slug = makeSlug(input);
                var id = $('#editModal').find('input[name=id]').val();

                if(input.match(slugRule)){
                    $('.edit_slug').val(slug);
                    return checkSlug(input, slug, 'edit', id);
                }

                $('.editSlugValidatation').addClass('d-none').removeClass('d-inline');
            }); 
 
            $('.edit_slug').on('input', function(){ 
                var input = $(this).val();
                var slug = makeSlug(input);
                var id = $('#editModal').find('input[name=id]').val();

                if(input.match(slugRule)){
                    $(this).val(slug);
                    return checkSlug(input, slug, 'edit', id);
                }
                
                $('.editSlugValidatation').addClass('d-none').removeClass('d-inline');
            });

            function checkSlug(input, slug, type, id = null){
                @permit('admin.check.slug')
                    $.ajax({
                        type:'POST',
                        url:'',
                        data: {
                            'input': input,
                            'id': id,
                            'model_type': 'service_category',
                            '_token': '{{ csrf_token() }}',
                        },

                        beforeSend: function() {
                            $(`.${type}SlugValidatation`).removeClass('d-none');
                            $(`.${type}SlugValidatation`).addClass('d-inline');
                            $(`.${type}SlugIcon`).html('<i class="fas fa-spinner fa-spin"></i>');
                            $(`.${type}AjaxResponse`).text('Validating...');
                        },

                        success:function(response){
                            setTimeout(function() {
                                if(response.error){
                                    $.each(response.error, function(key, value) {
                                        notify('error', value);
                                    });
                                    $(`.${type}SlugValidatation`).addClass('d-none');
                                    $(`.${type}SlugValidatation`).removeClass('d-inline');
                                }
                                else if(!response.success){
                                    $(`.${type}Url span`).text(slug);
                                    $(`.${type}SlugIcon`).html('');
                                    var message = `<span class='text--danger'>${response.message}</span>`;
                                    return $(`.${type}AjaxResponse`).html(message);
                                }
                                else if(response.success){
                                    $(`.${type}AjaxResponse`).html('');
                                    $(`.${type}SlugIcon`).html('');
                                    return $(`.${type}Url span`).text(slug);
                                }
                            }, 200);
                        }
                    });
                @endpermit
            }
            
        })(jQuery);
    </script>
@endpush

@push('style')
<style>
    .addUrl span, .editUrl span{ 
        border-bottom: 1px dashed;
    }
    .addUrl, .editUrl{ 
        font-weight: 500 !important;
    }
</style>
@endpush

 