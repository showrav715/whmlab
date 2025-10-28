<?php

namespace App\Providers;

use App\Constants\Status;
use App\Lib\Searchable;
use App\Models\AdminNotification;
use App\Models\CancelRequest;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\ServiceCategory;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
        if (!cache()->get('SystemInstalled')) {
            $envFilePath = base_path('.env');
            if (!file_exists($envFilePath)) {
                header('Location: install');
                exit;
            }
            $envContents = file_get_contents($envFilePath);
            if (empty($envContents)) {
                header('Location: install');
                exit;
            } else {
                cache()->put('SystemInstalled', true);
            }
        }


        $viewShare['emptyMessage'] = 'Data not found';
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount' => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount'   => User::mobileUnverified()->count(),
                'kycUnverifiedUsersCount'   => User::kycUnverified()->count(),
                'kycPendingUsersCount'   => User::kycPending()->count(),
                'pendingTicketCount'         => SupportTicket::whereIN('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount'    => Deposit::pending()->count(),
                'updateAvailable'    => version_compare(gs('available_version'),systemDetails()['version'],'>') ? 'v'.gs('available_version') : false,

                'pendingOrderCount' => Order::pending()->count(),
                'unpaidInvoiceCount'    => Invoice::unpaid()->count(),
                'pendingCancelRequestCount'    => CancelRequest::pending()->count(),
                'countAutomationError'    => AdminNotification::where('api_response', 1)->where('is_read', 0)->count(),
            ]);
        });

        $activeTemplate = activeTemplate();
        view()->composer([$activeTemplate.'partials.header', $activeTemplate.'layouts.side_bar', $activeTemplate.'layouts.master_side_bar'], function ($view) {
            $view->with([
                'serviceCategories'=> ServiceCategory::active()->get(['id', 'name', 'slug']),
            ]);
        });

        view()->composer('admin.partials.topnav', function ($view) {
            $view->with([
                'adminNotifications' => AdminNotification::where('is_read', Status::NO)->with('user')->orderBy('id', 'desc')->take(10)->get(),
                'adminNotificationCount' => AdminNotification::where('is_read', Status::NO)->count(),
            ]);
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if (gs('force_ssl')) {
            \URL::forceScheme('https');
        }
      
        Paginator::useBootstrapFive();
    }
}
