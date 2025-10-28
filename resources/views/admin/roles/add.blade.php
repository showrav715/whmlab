@extends('admin.layouts.app')
@section('panel')
    <form action="{{ route('admin.roles.save', @$role->id) }}" method="post">
        @csrf
        <div class="row gy-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">@lang('Name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', @$role->name) }}">
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Set Permissions')</h5>
                    </div>
                    <div class="card-body">
                        <div class="">

                            <div class="row gy-4">
                                @foreach ($permissionGroups as $key => $permissionGroup)
                                    <div class="col-12">
                                        <div class="permission-item">
                                            <div class="row gy-2 justify-content-center align-items-center">
                                                <div class="col-sm-3">
                                                    @php 
                                                        $string = Str::replaceLast('Controller', '', $key);
                                                        $group = camelCaseToNormal($string);
                                                    @endphp
                                                    <span>{{ $group }}</span>
                                                </div>
                                                <div class="col-sm-9">
                                                    <div class="d-flex flex-wrap gap-3">
                                                        @foreach ($permissionGroup as $permission)
                                                            <div class="custom-control custom-checkbox form-check-primary">
                                                                <input type="checkbox" class="custom-control-input" name="permissions[]" value="{{ $permission->id }}" id="customCheck{{ $permission->id }}">
                                                                <label class="custom-control-label" for="customCheck{{ $permission->id }}">{{ $permission->name }}</label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @permit('admin.roles.save')
                <div class="col-lg-12">
                    <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                </div>
            @endpermit
        </div>
    </form>
@endsection

@permit('admin.roles.index')
    @push('breadcrumb-plugins')
        <x-back route="{{ route('admin.roles.index') }}" />
    @endpush
@endpermit


@push('style')
    <style>
        .permission-item {
            background: #fafafa;
            border: 1px solid #f7f7f7;
            padding: 1rem;
        }
    </style>
@endpush

@push('script')
    @push('script')
        <script>
            (function($) {
                "use strict";
                @isset($permissions)
                    $('input[name="permissions[]"]').val(@json($permissions));
                @endif
            })(jQuery);
        </script>
    @endpush
@endpush
