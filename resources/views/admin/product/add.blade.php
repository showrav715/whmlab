@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12"> 
            <div class="card">
                <form class="form-horizontal" method="post" action="{{ route('admin.product.add') }}">
                    @csrf 
                    <div class="card-body">   
                        <div class="row"> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Product Type')</label>
                                    <select name="product_type" class="form-control" required>
                                        <option value="" hidden>@lang('Select One')</option>
                                        @foreach (productType() as $index => $type)
                                            <option value="{{ $index }}">{{ __($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Service Category')</label>
                                    <select name="service_category" class="form-control" required>
                                        <option value="" hidden>@lang('Select One')</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" data-slug="{{ $category->slug }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="input--group has_append mb-3">
                                    <label class="w-100">@lang('Name')</label>
                                    <input type="text" name="name" class="form-control readonly" required value="{{ old('name') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="input--group has_append">
                                    <div class="justify-content-between d-flex w-100">
                                        <label>@lang('Slug')</label>
                                        <div class="slugValidatation d-none">
                                            <i>
                                                <span class="slugIcon"></span>
                                                <small class="ajaxResponse">@lang('Validating')...</small>
                                            </i>
                                        </div>
                                    </div> 
                                    <input type="text" name="slug" class="form-control readonly" required value="{{ old('slug') }}" oninput="this.value = this.value.replace(/[^a-z0-9\s -]/gi, '')" readonly>

                                    @permit('admin.check.slug')
                                        <code class="fw-500 w-100 mt-2 slugUrl">
                                            {{ route('home') }}/store/<span class="categorySlug text--primary"></span><span class="productSlug text--primary">{{ old("slug") }}</span>
                                        </code>
                                    @endpermit
                                </div>
                            </div>
    
                            <div class="border-line-area style-two mt-4">
                                <h5 class="border-line-title">@lang('Module Settings')</h5>
                            </div>
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input--group has_append mb-3">
                                            <label class="w-100">@lang('Server Group')</label>
                                            <select name="server_group" class="form-control">
                                                <option value="">@lang('None')</option>
                                                @foreach ($serverGroups as $serverGroup)
                                                    <option value="{{ $serverGroup->id }}">{{ __($serverGroup->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="hidden" name="server_id" class="server_id">
                                        <div class="input--group has_append mb-3">
                                            <label class="w-100">@lang('Package Name') 
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                            </label>
                                            <select name="package_name" class="form-control">
                                                <option value="">@lang('Select One')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <div class="input--group has_append">
                                            @foreach(productModuleOptions() as $index => $data)
                                                <div class="d-flex w-100 gap-2 mb-2">
                                                    <input type="radio" name="module_option" id="{{ $index }}" value="{{ $index }}"> 
                                                    <label for="{{ $index }}" class="defaultLabel flex-grow-1 text-dot lh-1 mb-0">{{ __($data) }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>
                    @permit('admin.product.add')
                        <div class="card-footer mt-2">
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        </div>
                    @endpermit
                </form>
            </div>
        </div>
    </div>  
@endsection

@permit('admin.products')
    @push('breadcrumb-plugins')
        <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline--primary">
            <i class="la la-undo"></i> @lang('Go to Products')
        </a>
    @endpush
@endpermit
 
@push('script')
    <script>
        (function($){
            "use strict"; 

            $('select[name=server_group]').on('change', function(){
                var id = $(this).val();
                
                if(!id){
                    return false;
                }

                @permit('admin.get.server.package')
                    $.ajax({
                        type: 'post',
                        url: '{{ route("admin.get.server.package") }}',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'server_group_id': id
                        },
                        beforeSend: function(){
                            $('.spinner-border').removeClass('d-none');
                        },
                        complete: function(){
                            $('.spinner-border').addClass('d-none');
                        }, 
                        success: function (response){
                
                            $('select[name=package_name] option:not(:first)').remove();
                
                            if(response.success){ 
                                Object.entries(response.data).forEach(function(data, index){
                                    data[1].forEach(function(value){
                                        var name = value.split('_').pop();
                                        $('select[name=package_name]').append($('<option>', {value: value, text: value}).attr('data-server_id', data[0]));
                                    });
                                });
                            }else{
                                notify('error', response.message);
                            }
                        },

                    });
                @endpermit
            });

            $('select[name=package_name]').on('change', function(){
                var serverId = $(this).children('option:selected').data('server_id');
                $('.server_id').val(serverId);
            });

            var form = $('.form-horizontal');

            var oldProductType = '{{ old("product_type") }}';
            var oldCategory = '{{ old("service_category") }}';

            if(oldProductType){
                $('select[name=product_type]').val(oldProductType);
            }
            if(oldCategory){
                $('select[name=service_category]').val(oldCategory);
                $('.categorySlug').text($('select[name=service_category]').find(':selected').data('slug')+'/');
            } 

            form.find('select[name=server_group]').on('change',  function(){
                var value = $(this).val();
                
                if(value == 0){
                    $('select[name=package_name]').prop('disabled', true);  
                    return form.find('input[name=module_option]').prop({
                        'disabled': true,
                        'checked': true
                    });
                }
                
                $('select[name=package_name]').prop('disabled', false);
                return form.find('input[name=module_option]').prop({
                    'disabled': false
                });
            }).change();

            form.find('select[name=service_category]').on('change',  function(){
                var value = $(this).val();
                
                if(value){

                    form.find('.readonly').prop('readonly', true);
                    var name = form.find('input[name=name]').val();
                    var slug = form.find('input[name=slug]').val();

                    if(slug){
                        checkSlug(slug, makeSlug(slug), value);
                    }else if(name){
                        checkSlug(name, makeSlug(name), value);
                    }
                    
                    $('.categorySlug').text($(this).find(':selected').data('slug')+'/');
                    return form.find('.readonly').prop('readonly', false);
                }

                form.find('.readonly').prop('readonly', true);

            }).change();

            var slugRule = /^[0-9a-zA-Z -]+$/;

            form.find('input[name=name]').on('input',  function(){
                var input = $(this).val();
                var slug = makeSlug(input);
                var category = $('select[name=service_category]').val();

                if(!category){
                    return notify('info', 'Please select service category');
                }

                if(input.match(slugRule)){
                    form.find('input[name=slug]').val(slug);
                    return checkSlug(input, slug, category);
                }
            });

            form.find('input[name=slug]').on('input',  function(){
                var input = $(this).val();
                var slug = makeSlug(input);
                var category = $('select[name=service_category]').val();

                if(!category){
                    return notify('info', 'Please select service category');
                }

                if(input.match(slugRule)){
                    $(this).val(slug); 
                    return checkSlug(input, slug, category);
                }
            });

            function makeSlug(input){
                return input.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
            }

            function checkSlug(input, slug, category){
                @permit('admin.check.slug')
                    $.ajax({
                        type:'POST',
                        url:'{{ route("admin.check.slug") }}',
                        data: {
                            'input': input,
                            'model_type': 'product',
                            'category_id': category,
                            '_token': '{{ csrf_token() }}',
                        },

                        beforeSend: function() {
                            $('.slugValidatation').removeClass('d-none');
                            $('.slugValidatation').addClass('d-inline');
                            $('.slugIcon').html('<i class="fas fa-spinner fa-spin"></i>');
                            $('.ajaxResponse').text('Validating...');
                        }, 

                        success:function(response){
                            setTimeout(function() { 
                                if(response.error){
                                    $.each(response.error, function(key, value) {
                                        notify('error', value);
                                    });
                                    $('.slugValidatation').addClass('d-none');
                                    $('.slugValidatation').removeClass('d-inline');
                                } 
                                else if(!response.success){
                                    $('.productSlug').text(slug);
                                    $('.slugIcon').html('');
                                    var message = `<span class='text--danger'>${response.message}</span>`;
                                    return $('.ajaxResponse').html(message);
                                }
                                else if(response.success){
                                    $('.ajaxResponse').text('');
                                    $('.slugIcon').html('');
                                    return $('.productSlug').text(slug);
                                }
                            }, 300);
                        }
                    });
                @endpermit
            }

        })(jQuery);    
    </script> 
@endpush

@push('style')
    <style>
        .slugUrl span{ 
            border-bottom: 1px dashed;
        }
        .slugUrl{ 
            font-size: 14px;
        }
        .defaultLabel {
            font-size: initial;
        } 
        .text-dot{  
            height: inherit; 
        }
        .card[class*="border"] {
            border: 1px solid;
        }
    </style>
@endpush
