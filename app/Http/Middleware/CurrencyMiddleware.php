<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user wants to change currency
        if ($request->has('currency') && $request->get('currency')) {
            $currencyCode = $request->get('currency');
            $currency = Currency::where('code', $currencyCode)->active()->first();
            
            if ($currency) {
                session(['user_currency' => $currency->code]);
            }
        }
        
        // Set default currency if none in session
        if (!session('user_currency')) {
            $defaultCurrency = Currency::getDefault();
            session(['user_currency' => $defaultCurrency->code]);
        }
        
        // Make current currency available globally
        $currentCurrency = Currency::where('code', session('user_currency'))->first();
        if (!$currentCurrency || !$currentCurrency->status) {
            // Fallback to default if current currency is inactive
            $currentCurrency = Currency::getDefault();
            session(['user_currency' => $currentCurrency->code]);
        }
        
        // Share with all views
        view()->share('currentCurrency', $currentCurrency);
        view()->share('availableCurrencies', Currency::active()->ordered()->get());
        
        return $next($request);
    }
}