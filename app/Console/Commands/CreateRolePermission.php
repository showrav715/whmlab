<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class CreateRolePermission extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create necessary files and write codes to setup role permission module';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->createPermissionModel();
        $this->createRoleModel();
        $this->createPermissionsTable();
        $this->createRolesTable();
        $this->createPermissionRoleTable();
        $this->addNewColumnsToAdminsTable();
        $this->addRoleMethodToAdminModel();
        $this->createBladeServiceProvider();
        $this->registerBladeServiceProvider();
        $this->createPermissionMiddleware();
        $this->registerPermissionMiddleware();
        $this->createPermissionController();
        $this->createPermissionViews();
        $this->createRolesController();
        $this->createRoleViews();
        $this->createStaffController();
        $this->createStaffViews();
        $this->info('Role Permission module created successfully!');
    }

    private function createPermissionModel() {
        $filePath = app_path('Models\Permission.php');
        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        <?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Model;

        class Permission extends Model {

            public \$timestamps = false;

            public function roles() {
                return \$this->belongsToMany(Role::class);
            }
        }

        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function createRoleModel() {
        $filePath = app_path('Models\Role.php');
        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        <?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Support\Facades\Auth;
        use App\Models\Permission;

        class Role extends Model {

            public function permissions() {
                return \$this->belongsToMany(Permission::class);
            }

            public static function hasPermission(\$code = null) {
                \$admin  = Auth::guard('admin')->user();

                if (\$admin->id > 1) {
                    \$routeName      = \$code ?? str_replace('admin.', '', request()->route()->getName());
                    \$permissions    = \$admin->role->permissions->pluck('code')->toArray();
                    \$allPermissions = Permission::select('code')->get()->pluck('code')->toArray();

                    if (in_array(\$routeName, \$allPermissions) && !in_array(\$routeName, \$permissions)) {
                        return false;
                    }
                }

                return true;
            }
        }
        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function createPermissionsTable() {
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('group', 40)->nullable();
                $table->string('code', 255)->nullable();
            });
        }
    }

    private function createRolesTable() {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->timestamps();
            });
        }
    }

    private function createPermissionRoleTable() {
        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
            });
        }
    }

    private function addNewColumnsToAdminsTable() {
        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'role_id')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->unsignedInteger('role_id')->after('id');
            });
        }

        if (Schema::hasTable('admins') && !Schema::hasColumn('admins', 'status')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->boolean('status')->default(1)->after('password');
            });
        }
    }

    private function addRoleMethodToAdminModel() {

        if (method_exists('\App\Models\Admin', 'role')) {
            return;
        }

        $adminModelPath = app_path('Models\Admin.php');
        $adminModelContents = file_get_contents($adminModelPath);

        $newFunctionCode = "
    public function role(){
        return \$this->belongsTo(Role::class);
    }
    ";

        // Find the position to insert the new function code
        $insertPosition = strrpos($adminModelContents, '}');

        $existingFunctionPosition = strpos($adminModelContents, 'function role');

        if ($existingFunctionPosition === false) {
            $adminModelContents = substr_replace($adminModelContents, $newFunctionCode . PHP_EOL, $insertPosition, 0);
            file_put_contents($adminModelPath, $adminModelContents);
        }
    }

    private function createBladeServiceProvider() {
        $filePath = app_path('Providers\BladeDirectivesServiceProvider.php');
        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        <?php

        namespace App\Providers;

        use Illuminate\Support\Facades\Blade;
        use Illuminate\Support\ServiceProvider;

        class BladeDirectivesServiceProvider extends ServiceProvider {
            /**
             * Bootstrap the application services.
             *
             * @return void
             */
            public function boot() {
                Blade::directive('can', function (\$code) {
                    return '<?php \$hasPermission = App\Models\Role::hasPermission(' . \$code . ')  ? 1 : 0;
                    if(\$hasPermission == 1): ?>';
                });

                Blade::directive('endcan', function () {
                    return "<?php endif ?>";
                });
            }

            /**
             * Register the application services.
             *
             * @return void
             */
            public function register() {
                //
            }
        }



        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function registerBladeServiceProvider() {
        if (in_array(\App\Providers\BladeDirectivesServiceProvider::class, array_keys(app()->getLoadedProviders()))) {
            return;
        }

        $configPath = config_path('app.php');
        $configContents = file_get_contents($configPath);

        $newProvider = "'providers' => [
        // Existing providers...
        App\Providers\BladeDirectivesServiceProvider::class,";

        $configContents = str_replace("'providers' => [", $newProvider, $configContents);
        file_put_contents($configPath, $configContents);
    }


    private function createPermissionMiddleware() {
        $filePath = app_path('Http\Middleware\AdminPermissionMiddleware.php');
        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        <?php

        namespace App\Http\Middleware;

        use App\Models\Role;
        use Closure;
        use Illuminate\Http\Request;

        class AdminPermissionMiddleware {
            /**
             * Handle an incoming request.
             *
             * @param  \Illuminate\Http\Request  \$request
             * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  \$next
             * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
             */
            public function handle(Request \$request, Closure \$next) {

                if (!Role::hasPermission()) {
                    return redirect()->route('admin.profile');
                }
                return \$next(\$request);
            }
        }


        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function registerPermissionMiddleware() {
        $routeMiddleware = Route::getMiddleware();
        if (in_array('App\Http\Middleware\AdminPermissionMiddleware', $routeMiddleware)) {
            return 0;
        }

        $kernelPath = app_path('Http\Kernel.php');
        $kernelContents = file_get_contents($kernelPath);
        $newMiddleware = "protected \$routeMiddleware = [
        'adminPermission' => \App\Http\Middleware\AdminPermissionMiddleware::class,";

        $kernelContents = str_replace("protected \$routeMiddleware = [", $newMiddleware, $kernelContents);
        file_put_contents($kernelPath, $kernelContents);
    }

    private function createPermissionController() {
        $filePath = app_path('Http\Controllers\Admin\PermissionController.php');

        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        <?php

        namespace App\Http\Controllers\Admin;

        use App\Http\Controllers\Controller;
        use App\Models\Permission;
        use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Route;

        class PermissionController extends Controller {

            public function index() {
                \$pageTitle = 'All Permissions';
                \$permissions = Permission::all()->groupBy('group');
                \$routes = \$this->getPermissions();
                return view('admin.permission.index', compact('permissions', 'pageTitle', 'routes'));
            }

            public function updatePermissions(Request \$request) {

                \$request->validate([
                    'permission' => 'nullable|array',
                    'permission.*.id' => 'required|integer|gt:0',
                    'permission.*.name' => 'required|string'
                ]);

                foreach (\$request->permission as \$permission) {
                    \$permission = Permission::where('id', \$permission['id'])->update(['name' => \$permission['name']]);
                }

                \$notify[] = ['success', 'Updated successfully'];
                return back()->withNotify(\$notify);
            }

            public function getPermissions() {
                \$excludedControllers = ['LoginController', 'ForgotPasswordController', 'ResetPasswordController', 'PermissionController', 'AdminController@profile', 'AdminController@profileUpdate', 'AdminController@password', 'AdminController@passwordUpdate'];

                return collect(collect(Route::getRoutes())
                    ->filter(function (\$route) use (\$excludedControllers) {
                        return str_starts_with(\$route->getName(), 'admin.') && !in_array(last(array_reverse(explode('@', class_basename(\$route->getAction('controller'))))), \$excludedControllers) && !in_array(class_basename(\$route->getAction('controller')), \$excludedControllers);
                    })
                    ->map(function (\$route) {
                        \$controller = explode('@', class_basename(\$route->getActionName()));
                        return [
                            'name' => \$route->getName(),
                            'method' => @array_shift(\$route->methods),
                            'controller' => @\$controller[0],
                            'action' => last(\$controller)
                        ];
                    }));
            }
        }


        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function createPermissionViews() {
        $path = resource_path('views\admin\permission');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filePath = "$path\index.blade.php";

        if (file_exists($filePath)) {
            return;
        }


        $fileContent = <<<EOD
        @extends('admin.layouts.app')
        @section('panel')
            <form action="{{ route('admin.permissions.update') }}" method="POST">
                @csrf
                <div class="row gy-4">
                    @php
                        \$i = 0;
                    @endphp
                    @foreach (\$permissions as \$key => \$permissionGroups)
                        <div class="col-12">
                            <div class="card b-radius--10">
                                <div class="card-header">
                                    <h6 class="card-title m-0">{{ \$key }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row gx-5">

                                        @foreach (\$permissionGroups as \$permission)
                                            @php
                                                \$route = \$routes->where('name', \$permission->code)->first();
                                                \$lastCharacter = substr(\$permission->code, -1);
                                            @endphp
                                            <div class="col-lg-4">
                                                <div class="form-group ">
                                                    <div class="d-flex flex-wrap gap-1 align-items-center mb-1">
                                                        <label class="{{ \$lastCharacter == '.' ? 'bg--danger' : 'text--cyan' }}">{{ \$permission->code }}</label>
                                                        <span class="badge @if (\$route['method'] == 'GET') bg--success @else bg--danger @endif">
                                                            {{ \$route['method'] }}
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="permission[{{ \$i }}][id]" value="{{ \$permission->id }}">
                                                    <div class="input-group w-auto">
                                                        <span class="input-group-text">@lang('Name')</span>
                                                        <input type="text" name="permission[{{ \$i }}][name]" placeholder="Name" class="form-control" value="{{ ucwords(\$permission->name) }}">
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                \$i++;
                                            @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>

                        @php
                            \$i++;
                        @endphp
                    @endforeach
                </div>
                <button type="submit" class="btn btn--primary w-100 mt-3">@lang('Update')</button>
            </form>
        @endsection

        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function createRolesController() {
        $filePath = app_path('Http\Controllers\Admin\RolesController.php');

        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        <?php

        namespace App\Http\Controllers\Admin;

        use App\Http\Controllers\Controller;
        use App\Models\Permission;
        use App\Models\Role;
        use Illuminate\Http\Request;

        class RolesController extends Controller {

            public function index() {
                \$roles = Role::all();
                \$pageTitle = "All Roles";
                return view('admin.roles.index', compact('roles', 'pageTitle'));
            }

            public function add() {
                \$pageTitle = "Add New Role";
                \$permissionGroups = Permission::all()->groupBy('group');
                return view('admin.roles.add', compact('pageTitle', 'permissionGroups'));
            }

            public function edit(\$id) {
                \$pageTitle = "Edit Role";
                \$role = Role::with('permissions')->findOrFail(\$id);
                \$permissions = \$role->permissions->pluck('pivot.permission_id');
                \$permissionGroups = Permission::all()->groupBy('group');
                return view('admin.roles.add', compact('pageTitle', 'permissionGroups', 'role', 'permissions'));
            }

            public function save(Request \$request, \$id = 0) {
                \$request->validate([
                    'name'          => 'required|string',
                    'permissions'   => 'nullable|array',
                    'permissions.*' => 'required|integer',
                ]);

                if (!\$id) {
                    \$role = new Role();
                    \$notification = 'New role added successfully';
                } else {
                    \$role = Role::findOrFail(\$id);
                    \$notification = 'New role updated successfully';
                }
                \$role->name = \$request->name;
                \$role->save();

                \$role->permissions()->sync(\$request->permissions);
                \$notify[] = ['success', \$notification];
                return back()->withNotify(\$notify);
            }
        }


        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function createRoleViews() {
        $path = resource_path('views\admin\roles');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $this->roleIndexView($path);
        $this->roleAddView($path);
    }

    private function roleIndexView($path) {

        $filePath = "$path\index.blade.php";

        if (file_exists($filePath)) {
            return;
        }


        $fileContent = <<<EOD
        @extends('admin.layouts.app')
        @section('panel')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card b-radius--10">
                        <div class="card-body p-0">
                            <div class="table-responsive--md table-responsive">
                                <table class="table--light style--two table">

                                    <thead>
                                        <tr>
                                            <th>@lang('Name')</th>
                                            <th>@lang('Created At')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse (\$roles as \$role)
                                            <tr>
                                                <td>{{ \$role->name }}</td>
                                                <td>{{ showDateTime(\$role->created_at) }}</td>
                                                <td>
                                                    <a href="{{ route('admin.roles.edit', \$role->id) }}" class="btn btn-sm btn-outline--primary">@lang('Edit')</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-muted text-center" colspan="100%">{{ __(\$emptyMessage) }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @push('breadcrumb-plugins')
            <a href="{{ route('admin.roles.add') }}" class="btn btn-outline--primary">@lang('Add New')</a>
        @endpush


        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function roleAddView($path) {
        $filePath = "$path\add.blade.php";

        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        @extends('admin.layouts.app')
        @section('panel')
            <form action="{{ route('admin.roles.save', @\$role->id) }}" method="post">
                @csrf
                <div class="row gy-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">@lang('Name')</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', @\$role->name) }}">
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
                                        @foreach (\$permissionGroups as \$key => \$permissionGroup)
                                            <div class="col-12">
                                                <div class="permission-item">
                                                    <div class="row gy-2 justify-content-center align-items-center">
                                                        <div class="col-sm-3">
                                                            <span>{{ Str::replaceLast('Controller', '', \$key) }}</span>
                                                        </div>
                                                        <div class="col-sm-9">
                                                            <div class="d-flex flex-wrap gap-3">
                                                                @foreach (\$permissionGroup as \$permission)
                                                                    <div class="custom-control custom-checkbox form-check-primary">
                                                                        <input type="checkbox" class="custom-control-input" name="permissions[]" value="{{ \$permission->id }}" id="customCheck{{ \$permission->id }}">
                                                                        <label class="custom-control-label" for="customCheck{{ \$permission->id }}">{{ \$permission->name }}</label>
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

                    <div class="col-lg-12">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </div>
            </form>
        @endsection

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
                        @isset(\$permissions)
                            $('input[name="permissions[]"]').val(@json(\$permissions));
                        @endif
                    })(jQuery);
                </script>
            @endpush
        @endpush


        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function createStaffController() {
        $filePath = app_path('Http\Controllers\Admin\StaffController.php');

        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        <?php

        namespace App\Http\Controllers\Admin;

        use App\Http\Controllers\Controller;
        use App\Models\Admin;
        use App\Models\Role;
        use Illuminate\Http\Request;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Facades\Hash;

        class StaffController extends Controller {

            public function index() {
                \$pageTitle = 'All Staff';
                \$allStaff = Admin::with('role')->searchable(['username'])->paginate(getPaginate());
                \$roles = Role::all();
                return view('admin.staff.index', compact('pageTitle', 'allStaff', 'roles'));
            }

            public function status(\$id) {
                return Admin::changeStatus(\$id);
            }

            public function save(Request \$request, \$id = 0) {

                \$this->validation(\$request, \$id);
                if (\$id) {
                    \$staff   = Admin::findOrFail(\$id);
                    \$message = "Staff updated successfully";
                } else {
                    \$staff   = new Admin();
                    \$message = "New staff added successfully";
                }

                \$staff->name        = \$request->name;
                \$staff->username    = \$request->username;
                \$staff->email       = \$request->email;
                \$staff->role_id     = \$request->role_id;
                \$staff->password    = \$request->password ? Hash::make(\$request->password) : \$staff->password;
                \$staff->save();
                \$notify[] = ['success', \$message];
                return back()->withNotify(\$notify);
            }

            private function validation(\$request, \$id) {
                \$request->validate([
                    'username'    => 'required|unique:admins,username,' . \$id,
                    'name'        => 'required',
                    'email'       => 'required|unique:admins,email,' . \$id,
                    'role_id'     => 'required|integer|gt:0',
                    'password'    => !\$id ? 'required|min:6' : 'nullable',
                ]);
            }

            public function login(\$id) {
                Auth::guard('admin')->loginUsingId(\$id);
                return to_route('admin.dashboard');
            }
        }


        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    private function createStaffViews() {
        $path = resource_path('views\admin\staff');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filePath = "$path\index.blade.php";

        if (file_exists($filePath)) {
            return;
        }

        $fileContent = <<<EOD
        @extends('admin.layouts.app')
        @section('panel')
            <div class="row">
                <div class="col-lg-12">
                    <div class="card b-radius--10">
                        <div class="card-body p-0">
                            <div class="table-responsive--sm table-responsive">
                                <table class="table--light style--two table">
                                    <thead>
                                        <tr>
                                            <th>@lang('S.N.')</th>
                                            <th>@lang('Username')</th>
                                            <th>@lang('Name')</th>
                                            <th>@lang('Email')</th>
                                            <th>@lang('Role')</th>
                                            <th>@lang('Status')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(\$allStaff as \$staff)
                                            <tr>
                                                <td>{{ \$loop->index + \$allStaff->firstItem() }}</td>
                                                <td>{{ \$staff->username }}</td>
                                                <td>{{ \$staff->name }}</td>
                                                <td>{{ \$staff->email }}</td>
                                                <td>
                                                    @if (\$staff->role)
                                                        {{ \$staff->role->name }}
                                                    @else
                                                        @lang('Super Admin')
                                                    @endif
                                                </td>

                                                <td>
                                                    @php
                                                        echo \$staff->statusBadge;
                                                    @endphp
                                                </td>

                                                <td>
                                                    <div class="button--group">
                                                        @if (\$staff->id > 1)
                                                            <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ \$staff }}" data-modal_title="@lang('Update Staff')">
                                                                <i class="la la-pencil"></i>@lang('Edit')
                                                            </button>

                                                            @if (\$staff->status)
                                                                <button class="btn btn-sm confirmationBtn btn-outline--danger" data-action="{{ route('admin.staff.status', \$staff->id) }}" data-question="@lang('Are you sure to ban this staff?')" type="button">
                                                                    <i class="las la-user-alt-slash"></i>@lang('Ban')
                                                                </button>
                                                            @else
                                                                <button class="btn btn-sm confirmationBtn btn-outline--success" data-action="{{ route('admin.staff.status', \$staff->id) }}" data-question="@lang('Are you sure to unban this staff?')" type="button">
                                                                    <i class="las la-user-check"></i>@lang('Unban')
                                                                </button>
                                                            @endif

                                                            <a class="btn btn-sm btn-outline--dark" href="{{ route('admin.staff.login', \$staff->id) }}" target="_blank">
                                                                <i class="las la-sign-in-alt"></i>@lang('Login')
                                                            </a>
                                                        @endif

                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td class="text-muted text-center" colspan="100%">{{ __(\$emptyMessage) }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if (\$allStaff->hasPages())
                            <div class="card-footer py-4">
                                {{ paginateLinks(\$allStaff) }}

                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <x-confirmation-modal />

            <!-- Create Update Modal -->
            <div class="modal fade" id="cuModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="las la-times"></i>
                            </button>
                        </div>

                        <form action="{{ route('admin.staff.save') }}" method="POST">
                            @csrf
                            <div class="modal-body">

                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Email')</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Role')</label>
                                    <select name="role_id" class="form-select" required>
                                        <option value="" disabled selected>@lang('Select One')</option>
                                        @foreach (\$roles as \$role)
                                            <option value="{{ \$role->id }}">{{ \$role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Password')</label>
                                    <div class="input-group">
                                        <input class="form-control" name="password" type="text" required>
                                        <button class="input-group-text generatePassword" type="button">@lang('Generate')</button>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endsection

        @push('breadcrumb-plugins')
            <x-search-form placeholder="Username" />
            <!-- Modal Trigger Button -->
            <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Staff')">
                <i class="las la-plus"></i>@lang('Add New')
            </button>
        @endpush

        @push('script')
            <script>
                (function($) {
                    "use strict";
                    $('.generatePassword').on('click', function() {
                        $(this).siblings('[name=password]').val(generatePassword());
                    });

                    $('.cuModalBtn').on('click', function() {
                        let passwordField = $('#cuModal').find($('[name=password]'));
                        let label = passwordField.parents('.form-group').find('label')
                        if ($(this).data('resource')) {
                            passwordField.removeAttr('required');
                            label.removeClass('required')
                        } else {
                            passwordField.attr('required', 'required');
                            label.addClass('required')
                        }
                    });

                    function generatePassword(length = 12) {
                        let charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+<>?/";
                        let password = '';

                        for (var i = 0, n = charset.length; i < length; ++i) {
                            password += charset.charAt(Math.floor(Math.random() * n));
                        }

                        return password
                    }
                })(jQuery);
            </script>
        @endpush


        EOD;

        $file = fopen($filePath, 'w');

        if ($file !== false) {
            fwrite($file, $fileContent);
            fclose($file);
        }
    }
}
