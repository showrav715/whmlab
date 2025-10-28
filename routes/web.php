<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});


Route::get('cron', 'CronController@cron')->name('cron');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

//Shopping Cart
Route::controller('CartController')->prefix('shopping/cart')->name('shopping.')->group(function(){
    Route::get('/','cart')->name('cart');
    Route::post('add/domain','addDomain')->name('cart.add.domain');
    Route::post('add/service','addService')->name('cart.add.service');
    Route::get('empty', 'empty')->name('cart.empty');
    Route::get('remove/{id}', 'remove')->name('cart.remove');
    Route::get('config/domain/{cartId}', 'configDomain')->name('cart.config.domain');
    Route::post('config/domain/update', 'configDomainUpdate')->name('cart.config.domain.update');
    Route::get('config/service/{cartId}', 'configService')->name('cart.config.service');
    Route::post('config/service/update', 'configServiceUpdate')->name('cart.config.service.update');
    Route::post('coupon', 'coupon')->name('cart.coupon'); 
    Route::post('coupon/remove', 'couponRemove')->name('cart.coupon.remove');  
    Route::post('domain/renew', 'domainRenew')->name('cart.domain.renew')->middleware('auth'); 
});

Route::controller('SiteController')->group(function () {
     
    Route::get('/store/{slug?}', 'serviceCategory')->name('service.category');
    Route::get('store/{categorySlug}/{productSlug}/{id}', 'productConfigure')->name('product.configure');

    Route::get('/register/domain', 'registerDomain')->name('register.domain');
    Route::get('/search/domain', 'searchDomain')->name('search.domain');

    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('/currency/{code}', 'changeCurrency')->name('currency.switch');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('announcements', 'blogs')->name('blogs');
    Route::get('announcements/{slug}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode','maintenance')->withoutMiddleware('maintenance')->name('maintenance');
    Route::post('subscribe', 'subscribe')->name('subscribe');

    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
