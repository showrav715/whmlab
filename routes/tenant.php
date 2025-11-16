<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenancyServiceProvider.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    // Tenant Home
    Route::get('/', 'SiteController@index')->name('tenant.home');
    
    // Debug route to check tenant database
    Route::get('/debug-tenant', function () {
        $tenant = tenant();
        $dbName = $tenant ? $tenant->database()->getName() : 'No tenant';
        $currentDb = config('database.connections.tenant.database');
        
        return [
            'tenant_id' => $tenant?->id,
            'tenant_name' => $tenant?->getSetting('name'),
            'expected_db' => $dbName,
            'current_db' => $currentDb,
            'tenancy_initialized' => tenancy()->initialized,
        ];
    });
    
    // All existing routes for tenant sites
    Route::controller('SiteController')->group(function () {
        Route::get('/pages/{slug}', 'pages')->name('pages');
        Route::get('/contact', 'contact')->name('contact');
        Route::post('/contact', 'contactSubmit');
        Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
        Route::get('/currency/{code}', 'changeCurrency')->name('currency.switch');
        Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
        Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
        Route::get('announcements', 'blogs')->name('blogs');
        Route::get('announcements/{slug}', 'blogDetails')->name('blog.details');
        Route::get('policy/{slug}', 'policyPages')->name('policy.pages');
        Route::get('/register/domain', 'registerDomain')->name('register.domain');
        Route::get('/search/domain', 'searchDomain')->name('search.domain');
        Route::post('/subscribe', 'subscribe')->name('subscribe');
        Route::post('/search/domain', 'searchDomain');
        Route::get('/service/{slug?}', 'serviceCategory')->name('service.category');
        Route::get('/service/{categorySlug}/{productSlug}/{id}', 'productConfigure')->name('product.configure');
    });

    // User Authentication Routes
    Route::namespace('Auth')->name('user.')->prefix('user')->group(function () {
        Route::middleware('user.guest')->group(function () {
            // Login & Registration
            Route::controller('LoginController')->group(function () {
                Route::get('login', 'showLoginForm')->name('login');
                Route::post('login', 'login');
                Route::get('logout', 'logout')->withoutMiddleware('user.guest')->name('logout');
            });

            Route::controller('RegisterController')->group(function () {
                Route::get('register', 'showRegistrationForm')->name('register');
                Route::post('register', 'register')->middleware('registration.status');
            });

            Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
                Route::get('reset', 'showLinkRequestForm')->name('request');
                Route::post('email', 'sendResetCodeEmail')->name('email');
                Route::get('code-verify', 'codeVerify')->name('code.verify');
                Route::post('verify-code', 'verifyCode')->name('verify.code');
            });

            Route::controller('ResetPasswordController')->group(function () {
                Route::post('password/reset', 'reset')->name('password.update');
                Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
            });

            Route::controller('SocialiteController')->group(function () {
                Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
                Route::get('social-login-callback/{provider}', 'callback')->name('social.login.callback');
            });
        });
    });

    // Authenticated User Routes
    Route::middleware(['auth', 'check.status'])->name('user.')->prefix('user')->group(function () {
        // User Dashboard
        Route::get('dashboard', 'User\UserController@home')->name('home');
        Route::get('profile-setting', 'User\UserController@profile')->name('profile.setting');
        Route::post('profile-setting', 'User\UserController@submitProfile');
        Route::get('change-password', 'User\UserController@changePassword')->name('change.password');
        Route::post('change-password', 'User\UserController@submitPassword');

        // ... Add all other user routes here
    });

    // Shopping Cart Routes
    Route::controller('CartController')->prefix('shopping/cart')->name('shopping.')->group(function(){
        Route::get('/','cart')->name('cart');
        Route::post('add/domain','addDomain')->name('cart.add.domain');
        Route::post('add/service','addService')->name('cart.add.service');
        Route::get('empty', 'empty')->name('cart.empty');
        Route::get('remove/{id}', 'remove')->name('cart.remove');
        Route::get('config/domain/{id}', 'configDomain')->name('cart.config.domain');
        Route::get('config/service/{id}', 'configService')->name('cart.config.service');
        Route::post('config/domain/{id}', 'configDomainStore');
        Route::post('config/service/{id}', 'configServiceStore');
        Route::get('checkout', 'checkout')->name('cart.checkout');
        Route::post('checkout', 'checkoutStore');
    });

    // Support Ticket Routes
        Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
        Route::get('/', 'supportTicket')->name('index');
        Route::get('new', 'openSupportTicket')->name('open');
        Route::post('create', 'storeSupportTicket')->name('store');
        Route::get('view/{ticket}', 'viewTicket')->name('view');
        Route::post('reply/{id}', 'replyTicket')->name('reply');
        Route::post('close/{id}', 'closeTicket')->name('close');
        Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
    });
});
