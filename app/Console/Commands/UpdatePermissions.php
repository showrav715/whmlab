<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class UpdatePermissions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the permissions table if there are any new routes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $filePath = app_path('Models\Permission.php');
        if (!file_exists($filePath)) {
            $this->error('Permission model not found!');
            return;
        }
        $permission = new Permission();
        $excludedActions = $permission->excludedActions;

        $routesPermissions = collect(Route::getRoutes())
            ->filter(function ($route) use ($excludedActions) {
                return str_starts_with($route->getName(), 'admin.') && !in_array(last(array_reverse(explode('@', class_basename($route->getAction('controller'))))), $excludedActions) && !in_array(class_basename($route->getAction('controller')), $excludedActions);
            })
            ->map(function ($route) {
                $controller = explode('@', class_basename($route->getActionName()));
                return [
                    'code' => $route->getName(),
                    'name' => ucwords(str_replace('.', ' ', str_replace('admin.', '', $route->getName()))),
                    'group' => @$controller[0],
                ];
            });

        $newRoutes = [];
        $existingPermissions = Permission::pluck('code')->toArray();

        if (empty($existingPermissions)) {
            $newRoutes = $routesPermissions->toArray();
        }

        $permissions = $routesPermissions->pluck('code')->toArray();

        $updatablePermissions = array_diff($permissions, $existingPermissions);
        $deletablePermissions = array_diff($existingPermissions, $permissions);

        if (!empty($updatablePermissions)) {
            $newRoutes = $routesPermissions->whereIn('code', $updatablePermissions)->toArray();
        }

        if (!empty($newRoutes)) {
            Permission::insert($newRoutes);
        }

        if ($deletablePermissions) {
            $permissions = Permission::whereIn('code', $deletablePermissions)->with('roles')->get();
            foreach ($permissions as $permission) {
                $permission->roles()->detach();
                $permission->delete();
            }
        }

        return $this->info('Permissions table updated successfully!');
    }
}
