<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Auth')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/', 'showLoginForm')->name('login');
            Route::post('/', 'login')->name('login');
            Route::get('logout', 'logout')->middleware('admin')->withoutMiddleware('admin.guest')->name('logout');
        });

        // Admin Password Reset
        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('reset');
            Route::post('reset', 'sendResetCodeEmail');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset.form');
            Route::post('password/reset/change', 'reset')->name('password.change');
        });
    });
});

Route::middleware(['admin', 'admin.permission'])->group(function () {
    Route::controller('AdminController')->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('profile', 'profile')->name('profile');
        Route::post('profile', 'profileUpdate')->name('profile.update');
        Route::get('password', 'password')->name('password');
        Route::post('password', 'passwordUpdate')->name('password.update');

        Route::get('order-statistics', 'orderStatistics')->name('order.statistics');

        //Notification
        Route::get('notifications', 'notifications')->name('notifications');
        Route::get('notification/read/{id}', 'notificationRead')->name('notification.read');
        Route::get('notifications/read-all', 'readAllNotification')->name('notifications.read.all');
        Route::post('notifications/delete-all', 'deleteAllNotification')->name('notifications.delete.all');
        Route::post('notifications/delete-single/{id}', 'deleteSingleNotification')->name('notifications.delete.single');

        //Report Bugs
        Route::get('request-report', 'requestReport')->name('request.report');
        Route::post('request-report', 'reportSubmit');

        Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

        // Automation Error Routes
        Route::get('automation/errors', 'automationErrors')->name('automation.errors');
        Route::get('delete/automation/errors', 'deleteAutomationErrors')->name('delete.automation.errors');
        Route::get('read/automation/errors', 'readAutomationErrors')->name('read.automation.errors');
        Route::get('delete/automation/error/{id}', 'deleteAutomationError')->name('delete.automation.error');

        // Domain Management Route (WHMCS-style domains, not tenant domains)
        Route::get('domains', 'domains')->name('domains');

        // Services Management Route (WHMCS-style services/hosting)
        Route::get('services', 'services')->name('services');
    });


    // Tenant Management Routes (Multi-tenancy System)
    Route::controller('TenantController')->prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{tenant}', 'show')->name('show');
        Route::get('{tenant}/edit', 'edit')->name('edit');
        Route::put('{tenant}', 'update')->name('update');
        Route::delete('{tenant}', 'destroy')->name('destroy');
        Route::post('{tenant}/status', 'status')->name('status');
        Route::post('migrate', 'migrate')->name('migrate');

        // Tenant Subscription Management
        Route::get('{tenant}/subscriptions', 'subscriptions')->name('subscriptions');
        Route::post('{tenant}/subscriptions/add', 'addSubscription')->name('subscriptions.add');
        Route::delete('{tenant}/subscriptions/{subscription}', 'removeSubscription')->name('subscription.remove');
    });

    // Subscription Plan Management Routes
    Route::controller('SubscriptionPlanController')->prefix('subscription-plans')->name('subscription.plans.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('{plan}', 'show')->name('show');
        Route::get('{plan}/edit', 'edit')->name('edit');
        Route::put('{plan}', 'update')->name('update');
        Route::delete('{plan}', 'destroy')->name('destroy');
        Route::post('{plan}/status', 'status')->name('status');
    });

    //Service
    Route::controller('ServiceController')->group(function () {
        Route::get('hosting/details/{id}', 'hostingDetails')->name('order.hosting.details');
        Route::post('hosting/update/', 'hostingUpdate')->name('order.hosting.update');
        Route::get('change/order/hosting/product/{hostingId}/{productId}', 'ServiceController@changeHostingProduct')->name('change.order.hosting.product');

        Route::get('domain/details/{id}', 'domainDetails')->name('order.domain.details');
        Route::post('domain/update', 'domainUpdate')->name('order.domain.update');
    });





    //Service Cancel Request
    Route::controller('CancelRequestController')->group(function () {
        Route::get('cancel/requests', 'allRequests')->name('cancel.requests');
        Route::get('cancel/request/pending', 'pending')->name('cancel.request.pending');
        Route::get('cancel/request/completed', 'completed')->name('cancel.request.completed');
        Route::post('cancel/request', 'cancel')->name('cancel.request');
        Route::post('cancel/request/delete', 'delete')->name('cancel.request.delete');
    });

    Route::controller('StaffController')->prefix('staff')->name('staff.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::post('save/{id?}', 'save')->name('save');
        Route::post('switch-status/{id}', 'status')->name('status');
        Route::get('login/{id}', 'login')->name('login');
    });

    Route::controller('RolesController')->prefix('roles')->name('roles.')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('add', 'add')->name('add');
        Route::get('edit/{id}', 'edit')->name('edit');
        Route::post('save/{id?}', 'save')->name('save');
    });

    //Domain Setup / Tld
    Route::controller('TldController')->group(function () {
        Route::get('all/tld', 'all')->name('tld');
        Route::post('add/tld', 'add')->name('tld.add');
        Route::post('update/tld', 'update')->name('tld.update');
        Route::post('update/tld/pricing', 'updatePricing')->name('tld.update.pricing');
        Route::post('tld/status/{id}', 'status')->name('tld.status');
    });

    //Domain Register
    Route::controller('DomainRegisterController')->group(function () {
        Route::get('domain/registers', 'all')->name('register.domain');
        Route::post('domain/register/update', 'update')->name('register.domain.update');
        Route::post('auto/domain/register', 'autoRegister')->name('register.domain.auto');
        Route::post('domain/register/status/{id}', 'status')->name('register.domain.status');
    });

    //Configuration
    Route::controller('ConfigurableController')->group(function () {
        Route::get('configurable/groups', 'groups')->name('configurable.groups');
        Route::post('add/configurable/group', 'addGroup')->name('configurable.group.add');
        Route::post('update/configurable/group', 'updateGroup')->name('configurable.group.update');
        Route::post('configurable/group/status/{id}', 'groupStatus')->name('configurable.group.status');

        Route::get('configurable/group/{id}/options', 'options')->name('configurable.group.options');
        Route::post('add/configurable/group/option', 'addOption')->name('configurable.group.add.option');
        Route::post('update/configurable/group/option', 'updateOption')->name('configurable.group.update.option');
        Route::post('configurable/group/option/status/{id}', 'optionStatus')->name('configurable.group.option.status');

        Route::get('configurable/group/{groupId}/{optionId}/sub/options', 'subOptions')->name('configurable.group.sub.options');
        Route::post('configurable/group/add/sub/option', 'addSubOption')->name('configurable.group.add.sub.option');
        Route::post('configurable/group/update/sub/option', 'updateSubOption')->name('configurable.group.update.sub.option');
        Route::post('configurable/group/sub/option/status/{id}', 'subOptionStatus')->name('configurable.group.sub.option.status');
    });

    //Hosting Module
    Route::controller('HostingModuleController')->group(function () {
        Route::post('module/command', 'moduleCommand')->name('module.command');
        Route::post('login/hosting', 'loginHosting')->name('module.login.hosting');
    });

    //Domain Module
    Route::controller('DomainModuleController')->group(function () {
        Route::post('domain/module/command', 'moduleCommand')->name('domain.module.command');
        Route::get('domain/contact/details/{id}', 'domainContact')->name('order.domain.contact');
    });

    //Product
    Route::controller('ProductController')->group(function () {
        Route::get('products', 'products')->name('products');
        Route::get('add/product', 'addProductPage')->name('product.add.page');
        Route::post('add/product', 'addProduct')->name('product.add');
        Route::get('edit/product/{id}', 'editProductPage')->name('product.update.page');
        Route::post('update/product', 'updateProduct')->name('product.update');
        Route::post('product/status/{id}', 'status')->name('product.status');

        Route::post('get/server/package', 'getServerPackage')->name('get.server.package');
    });

    //Service Category
    Route::controller('ServiceCategoryController')->group(function () {
        Route::get('categories', 'all')->name('service.category');
        Route::post('add/category', 'add')->name('service.category.add');
        Route::post('update/category', 'update')->name('service.category.update');
        Route::post('status/{id}', 'status')->name('service.category.status');
    });

    //Server
    Route::controller('ServerController')->group(function () {
        Route::get('groups/server', 'groupsServer')->name('groups.server');
        Route::post('add/group/server', 'addGroupServer')->name('group.server.add');
        Route::post('update/group/server', 'updateGroupServer')->name('group.server.update');
        Route::post('group/server/status/{id}', 'groupServerStatus')->name('group.server.status');

        Route::get('servers', 'servers')->name('servers');
        Route::get('add/server', 'addServerPage')->name('server.add.page');
        Route::post('add/server', 'addServer')->name('server.add');
        Route::get('edit/server/{id}', 'editServerPage')->name('server.edit.page');
        Route::post('update/server', 'updateServer')->name('server.update');
        Route::get('server/login/{id}', 'serverLogin')->name('server.login');
        Route::post('server/status/{id}', 'serverStatus')->name('server.status');

        Route::post('server/test/connection', 'testConnection')->name('server.test.connection');
    });

    // Billing Setting
    Route::controller('BillingSettingController')->group(function () {
        Route::get('billing-setting', 'index')->name('billing.setting');
        Route::post('billing-setting', 'update')->name('billing.setting.update');
        Route::post('update/advance/billing/setting', 'advanceBillingSetting')->name('billing.setting.advanced');
    });

    //Coupon
    Route::controller('CouponController')->group(function () {
        Route::get('coupons', 'coupons')->name('coupons');
        Route::post('add/coupon', 'add')->name('coupon.add');
        Route::post('update/coupon', 'update')->name('coupon.update');
        Route::post('coupon/status/{id}', 'status')->name('coupon.status');
    });



    // Subscriber
    Route::controller('SubscriberController')->prefix('subscriber')->name('subscriber.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('send-email', 'sendEmailForm')->name('send.email');
        Route::post('remove/{id}', 'remove')->name('remove');
        Route::post('send-email', 'sendEmail')->name('send.email');
    });



    Route::middleware('superadmin')->group(function () {
        // Users Manager
        Route::controller('ManageUsersController')->name('users.')->prefix('clients')->group(function () {
            Route::get('/', 'allUsers')->name('all');
            Route::get('active', 'activeUsers')->name('active');
            Route::get('banned', 'bannedUsers')->name('banned');
            Route::get('email-verified', 'emailVerifiedUsers')->name('email.verified');
            Route::get('email-unverified', 'emailUnverifiedUsers')->name('email.unverified');
            Route::get('mobile-unverified', 'mobileUnverifiedUsers')->name('mobile.unverified');
            Route::get('kyc-unverified', 'kycUnverifiedUsers')->name('kyc.unverified');
            Route::get('kyc-pending', 'kycPendingUsers')->name('kyc.pending');
            Route::get('mobile-verified', 'mobileVerifiedUsers')->name('mobile.verified');
            Route::get('with-balance', 'usersWithBalance')->name('with.balance');

            Route::get('detail/{id}', 'detail')->name('detail');
            Route::get('kyc-data/{id}', 'kycDetails')->name('kyc.details');
            Route::post('kyc-approve/{id}', 'kycApprove')->name('kyc.approve');
            Route::post('kyc-reject/{id}', 'kycReject')->name('kyc.reject');
            Route::post('update/{id}', 'update')->name('update');
            Route::post('add-sub-balance/{id}', 'addSubBalance')->name('add.sub.balance');
            Route::get('send-notification/{id}', 'showNotificationSingleForm')->name('notification.single');
            Route::post('send-notification/{id}', 'sendNotificationSingle')->name('notification.single');
            Route::get('login/{id}', 'login')->name('login');
            Route::post('status/{id}', 'status')->name('status');

            Route::get('send-notification', 'showNotificationAllForm')->name('notification.all');
            Route::post('send-notification', 'sendNotificationAll')->name('notification.all.send');
            Route::get('list', 'list')->name('list');
            Route::get('count-by-segment/{methodName}', 'countBySegment')->name('segment.count');
            Route::get('notification-log/{id}', 'notificationLog')->name('notification.log');

            Route::get('orders/{id}', 'orders')->name('orders');
            Route::get('invoices/{id}', 'invoices')->name('invoices');
            Route::get('cancellations/{id}', 'cancellations')->name('cancellations');
            Route::get('services/{id}', 'services')->name('services');
            Route::get('domains/{id}', 'domains')->name('domains');

            Route::get('add/new', 'addNewForm')->name('add.new.form');
            Route::post('add/new', 'addNew')->name('add.new');
        });
        // Deposit Gateway
        Route::name('gateway.')->prefix('gateway')->group(function () {
            // Automatic Gateway
            Route::controller('AutomaticGatewayController')->prefix('automatic')->name('automatic.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('edit/{alias}', 'edit')->name('edit');
                Route::post('update/{code}', 'update')->name('update');
                Route::post('remove/{id}', 'remove')->name('remove');
                Route::post('status/{id}', 'status')->name('status');
            });


            // Manual Methods
            Route::controller('ManualGatewayController')->prefix('manual')->name('manual.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('new', 'create')->name('create');
                Route::post('new', 'store')->name('store');
                Route::get('edit/{alias}', 'edit')->name('edit');
                Route::post('update/{id}', 'update')->name('update');
                Route::post('status/{id}', 'status')->name('status');
            });
        });

        // DEPOSIT SYSTEM
        Route::controller('DepositController')->prefix('payment')->name('deposit.')->group(function () {
            Route::get('all/{user_id?}', 'deposit')->name('list');
            Route::get('pending/{user_id?}', 'pending')->name('pending');
            Route::get('rejected/{user_id?}', 'rejected')->name('rejected');
            Route::get('approved/{user_id?}', 'approved')->name('approved');
            Route::get('successful/{user_id?}', 'successful')->name('successful');
            Route::get('initiated/{user_id?}', 'initiated')->name('initiated');
            Route::get('details/{id}', 'details')->name('details');
            Route::post('reject', 'reject')->name('reject');
            Route::post('approve/{id}', 'approve')->name('approve');
        });

        //Order
        Route::controller('OrderController')->group(function () {
            Route::get('orders', 'orders')->name('orders');
            Route::get('pending/orders', 'pending')->name('orders.pending');
            Route::get('active/orders', 'active')->name('orders.active');
            Route::get('cancelled/orders', 'cancelled')->name('orders.cancelled');
            Route::get('order/details/{id}', 'details')->name('orders.details');
            Route::post('accept/order', 'accept')->name('order.accept');
            Route::post('cancel/order', 'cancel')->name('order.cancel');
            Route::post('mark-as-pending/order', 'markPending')->name('order.mark.pending');
            Route::post('order/notes', 'orderNotes')->name('order.notes');
        });

        //Invoice
        Route::controller('InvoiceController')->group(function () {
            Route::get('invoices', 'invoices')->name('invoices');
            Route::get('cancelled/invoices', 'cancelled')->name('invoices.cancelled');
            Route::get('paid/invoices', 'paid')->name('invoices.paid');
            Route::get('unpaid/invoices', 'unpaid')->name('invoices.unpaid');
            Route::get('payment-pending/invoices', 'paymentPending')->name('invoices.payment.pending');
            Route::get('refunded/invoices', 'refunded')->name('invoices.refunded');
            Route::get('invoice/details/{id}', 'details')->name('invoices.details');
            Route::post('invoice/update', 'updateInvoice')->name('invoice.update');
            Route::get('download/{id}/{view?}', 'download')->name('invoice.download');
            Route::post('delete/invoice/item', 'deleteInvoiceItem')->name('invoice.item.delete');
            Route::post('refund/invoice', 'refundInvoice')->name('invoice.refund');

            Route::get('domain/{id}/invoices', 'InvoiceController@domainInvoices')->name('invoices.domain.all');
            Route::get('hosting/{id}/invoices', 'InvoiceController@hostingInvoices')->name('invoices.hosting.all');

            Route::get('invoice/{id}/payment/transactions', 'InvoiceController@paymentTransactions')->name('invoices.payment.transactions');
        });
    });









    // Report
    Route::controller('ReportController')->prefix('report')->name('report.')->group(function () {
        Route::get('transaction/{user_id?}', 'transaction')->name('transaction');
        Route::get('login/history', 'loginHistory')->name('login.history');
        Route::get('login/ipHistory/{ip}', 'loginIpHistory')->name('login.ipHistory');
        Route::get('notification/history', 'notificationHistory')->name('notification.history');
        Route::get('email/detail/{id}', 'emailDetails')->name('email.details');
    });


    // Admin Support
    Route::controller('SupportTicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'tickets')->name('index');
        Route::get('pending', 'pendingTicket')->name('pending');
        Route::get('closed', 'closedTicket')->name('closed');
        Route::get('answered', 'answeredTicket')->name('answered');
        Route::get('view/{id}', 'ticketReply')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
        Route::post('delete/{id}', 'ticketDelete')->name('delete');
    });


    // Language Manager
    Route::controller('LanguageController')->prefix('language')->name('language.')->group(function () {
        Route::get('/', 'langManage')->name('manage');
        Route::post('/', 'langStore')->name('manage.store');
        Route::post('delete/{id}', 'langDelete')->name('manage.delete');
        Route::post('update/{id}', 'langUpdate')->name('manage.update');
        Route::get('edit/{id}', 'langEdit')->name('key');
        Route::post('import', 'langImport')->name('import.lang');
        Route::post('store/key/{id}', 'storeLanguageJson')->name('store.key');
        Route::post('delete/key/{id}', 'deleteLanguageJson')->name('delete.key');
        Route::post('update/key/{id}', 'updateLanguageJson')->name('update.key');
        Route::get('get-keys', 'getKeys')->name('get.key');
    });

    Route::controller('GeneralSettingController')->group(function () {

        Route::get('system-setting', 'systemSetting')->name('setting.system');

        // General Setting
        Route::get('general-setting', 'general')->name('setting.general');
        Route::post('general-setting', 'generalUpdate');

        Route::get('setting/social/credentials', 'socialiteCredentials')->name('setting.socialite.credentials');
        Route::post('setting/social/credentials/update/{key}', 'updateSocialiteCredential')->name('setting.socialite.credentials.update');
        Route::post('setting/social/credentials/status/{key}', 'updateSocialiteCredentialStatus')->name('setting.socialite.credentials.status.update');

        //configuration
        Route::get('setting/system-configuration', 'systemConfiguration')->name('setting.system.configuration');
        Route::post('setting/system-configuration', 'systemConfigurationSubmit');

        // Logo-Icon
        Route::get('setting/logo-icon', 'logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'logoIconUpdate')->name('setting.logo.icon.update');

        //Custom CSS
        Route::get('custom-css', 'customCss')->name('setting.custom.css');
        Route::post('custom-css', 'customCssSubmit');

        Route::get('sitemap', 'sitemap')->name('setting.sitemap');
        Route::post('sitemap', 'sitemapSubmit');

        Route::get('robot', 'robot')->name('setting.robot');
        Route::post('robot', 'robotSubmit');

        //Cookie
        Route::get('cookie', 'cookie')->name('setting.cookie');
        Route::post('cookie', 'cookieSubmit');

        //maintenance_mode
        Route::get('maintenance-mode', 'maintenanceMode')->name('maintenance.mode');
        Route::post('maintenance-mode', 'maintenanceModeSubmit');
    });

    // Currency Management
    Route::controller('CurrencyController')->name('currency.')->prefix('currency')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{currency}', 'edit')->name('edit');
        Route::put('update/{currency}', 'update')->name('update');
        Route::post('delete/{currency}', 'destroy')->name('destroy');
        Route::post('status/{id}', 'status')->name('status');
        Route::post('set-default/{id}', 'setDefault')->name('set.default');
        Route::get('update-rates', 'updateRates')->name('update.rates');
        Route::post('store-rates', 'storeRates')->name('store.rates');
    });

    Route::controller('CronConfigurationController')->name('cron.')->prefix('cron')->group(function () {
        Route::get('index', 'cronJobs')->name('index');
        Route::post('store', 'cronJobStore')->name('store');
        Route::post('update', 'cronJobUpdate')->name('update');
        Route::post('delete/{id}', 'cronJobDelete')->name('delete');
        Route::get('schedule', 'schedule')->name('schedule');
        Route::post('schedule/store', 'scheduleStore')->name('schedule.store');
        Route::post('schedule/status/{id}', 'scheduleStatus')->name('schedule.status');
        Route::get('schedule/pause/{id}', 'schedulePause')->name('schedule.pause');
        Route::get('schedule/logs/{id}', 'scheduleLogs')->name('schedule.logs');
        Route::post('schedule/log/resolved/{id}', 'scheduleLogResolved')->name('schedule.log.resolved');
        Route::post('schedule/log/flush/{id}', 'logFlush')->name('log.flush');
    });


    //KYC setting
    Route::controller('KycController')->group(function () {
        Route::get('kyc-setting', 'setting')->name('kyc.setting');
        Route::post('kyc-setting', 'settingUpdate');
    });

    //Notification Setting
    Route::name('setting.notification.')->controller('NotificationController')->prefix('notification')->group(function () {
        //Template Setting
        Route::get('global/email', 'globalEmail')->name('global.email');
        Route::post('global/email/update', 'globalEmailUpdate')->name('global.email.update');

        Route::get('global/sms', 'globalSms')->name('global.sms');
        Route::post('global/sms/update', 'globalSmsUpdate')->name('global.sms.update');

        Route::get('global/push', 'globalPush')->name('global.push');
        Route::post('global/push/update', 'globalPushUpdate')->name('global.push.update');

        Route::get('templates', 'templates')->name('templates');
        Route::get('template/edit/{type}/{id}', 'templateEdit')->name('template.edit');
        Route::post('template/update/{type}/{id}', 'templateUpdate')->name('template.update');

        //Email Setting
        Route::get('email/setting', 'emailSetting')->name('email');
        Route::post('email/setting', 'emailSettingUpdate');
        Route::post('email/test', 'emailTest')->name('email.test');

        //SMS Setting
        Route::get('sms/setting', 'smsSetting')->name('sms');
        Route::post('sms/setting', 'smsSettingUpdate');
        Route::post('sms/test', 'smsTest')->name('sms.test');

        Route::get('notification/push/setting', 'pushSetting')->name('push');
        Route::post('notification/push/setting', 'pushSettingUpdate');
        Route::post('notification/push/setting/upload', 'pushSettingUpload')->name('push.upload');
        Route::get('notification/push/setting/download', 'pushSettingDownload')->name('push.download');
    });

    // Plugin
    Route::controller('ExtensionController')->prefix('extensions')->name('extensions.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('update/{id}', 'update')->name('update');
        Route::post('status/{id}', 'status')->name('status');
    });


    //System Information
    Route::controller('SystemController')->name('system.')->prefix('system')->group(function () {
        Route::get('info', 'systemInfo')->name('info');
        Route::get('server-info', 'systemServerInfo')->name('server.info');
        Route::get('optimize', 'optimize')->name('optimize');
        Route::get('optimize-clear', 'optimizeClear')->name('optimize.clear');
        Route::get('system-update', 'systemUpdate')->name('update');
        Route::post('system-update', 'systemUpdateProcess')->name('update.process');
        Route::get('system-update/log', 'systemUpdateLog')->name('update.log');
    });

    // System Performance Monitor (cPanel optimized)
    Route::controller('SystemPerformanceController')->name('performance.')->prefix('system/performance')->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::get('health-check', 'healthCheck')->name('health.check');
        Route::get('test-external-db', 'testExternalDB')->name('test.external.db');
        Route::post('clear-caches', 'clearCaches')->name('clear.caches');
    });


    // SEO
    Route::get('seo', 'FrontendController@seoEdit')->name('seo');


    // Frontend
    Route::name('frontend.')->prefix('frontend')->group(function () {

        Route::controller('FrontendController')->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('templates', 'templates')->name('templates');
            Route::post('templates', 'templatesActive')->name('templates.active');
            Route::get('frontend-sections/{key?}', 'frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'frontendElement')->name('sections.element');
            Route::get('frontend-slug-check/{key}/{id?}', 'frontendElementSlugCheck')->name('sections.element.slug.check');
            Route::get('frontend-element-seo/{key}/{id}', 'frontendSeo')->name('sections.element.seo');
            Route::post('frontend-element-seo/{key}/{id}', 'frontendSeoUpdate');
            Route::post('remove/{id}', 'remove')->name('remove');
        });

        // Page Builder
        Route::controller('PageBuilderController')->group(function () {
            Route::get('manage-pages', 'managePages')->name('manage.pages');
            Route::get('manage-pages/check-slug/{id?}', 'checkSlug')->name('manage.pages.check.slug');
            Route::post('manage-pages', 'managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete/{id}', 'managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'manageSectionUpdate')->name('manage.section.update');

            Route::get('manage-seo/{id}', 'manageSeo')->name('manage.pages.seo');
            Route::post('manage-seo/{id}', 'manageSeoStore');
        });
    });
});
