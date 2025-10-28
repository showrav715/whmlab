<?php

namespace App\Http\Controllers;

use App\DomainRegisters\Register;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Domain;
use App\Models\DomainRegister;
use App\Models\DomainSetup;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;

class CartController extends Controller
{

    public function cart()
    {

        $pageTitle = 'Cart';
        $carts = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })
            ->with([
                'product' => function ($product) {
                    $product->select('id', 'category_id', 'name');
                },
                'product.serviceCategory' => function ($category) {
                    $category->select('id', 'name');
                }
            ])
            ->orderBy('id', 'DESC')
            ->get();

        //If coupon applied already
        $appliedCoupon = $carts->where('discount_applied', 1)->where('discount', '!=', 0)->first();

        return view('Template::user.cart', compact('pageTitle', 'carts', 'appliedCoupon'));
    }

    public function empty()
    {

        ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->delete();
        session()->forget('randomId');

        $notify[] = ['success', 'Removed all item successfully'];
        return back()->withNotify($notify);
    }

    public function remove($id)
    {

        $cart = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->findOrFail($id);

        $cart->delete();

        $notify[] = ['success', 'Cart item deleted successfully'];
        return back()->withNotify($notify);
    }

    public function addDomain(Request $request)
    {

        $defaultDomainRegister = DomainRegister::getDefault();
        if (!$defaultDomainRegister) {
            $notify[] = ['info', 'There is no default domain register, Please setup default domain register'];
            return redirect()->route('register.domain')->withNotify($notify);
        }

        $request->validate([
            'domain' => ['required', 'regex:/^[a-zA-Z0-9.-]+$/'],
            'domain_setup_id' => 'required|integer',
        ]);

        $domain = $request->domain;
        $cart = ShoppingCart::where('domain_setup_id', $request->domain_setup_id)->where('product_id', 0)->where('domain', $domain)->first();

        //If already exist
        if ($cart) {
            return redirect()->route('shopping.cart');
        }

        $register = new Register($defaultDomainRegister->alias); //The Register is a class
        $register->singleSearch = true;
        $register->command = 'searchDomain';
        $register->domain = $domain;
        $execute = $register->run();

        if (!$execute['success']) {
            $notify[] = ['error', $execute['message']];
            return redirect()->route('register.domain')->withNotify($notify);
        }

        if (@$execute['data']['status'] == 'ERROR') {
            $notify[] = ['error', $execute['data']['message']];
            return redirect()->route('register.domain')->withNotify($notify);
        }

        if (!@$execute['isSupported']) {
            $tld = @$execute['tld'];
            $notify[] = ['error', "We are not supporting $tld right now"];
            return redirect()->route('register.domain')->withNotify($notify);
        }

        $cart = new ShoppingCart();
        $cart->user_id = $this->userId();
        $cart->domain_setup_id = $request->domain_setup_id;
        $cart->type = 3; //3 means Only Domain
        /**
         * For knowing about type
         * @see \App\Models\ShoppingCart go to type method
         */
        $cart->reg_period = $execute['setup']->pricing->firstPrice['year'] ?? 0;
        $cart->price = $execute['setup']->pricing->firstPrice['price'] ?? 0;
        $cart->domain = $request->domain;
        $cart->setup_fee = 0;
        $cart->discount = 0;
        $cart->total = $cart->price;
        $cart->after_discount = $cart->price;

        $cart->domain_register_id = $defaultDomainRegister->id;

        $cart->save();

        $notify[] = ['success', 'Domain added to cart successfully'];
        return to_route('shopping.cart.config.domain', $cart->id)->withNotify($notify);
    }

    public function domainRenew(Request $request)
    {

        $request->validate([
            'domain_id' => 'required|integer',
            'renew_year' => 'required|integer|between:1,6',
        ]);

        $user = auth()->user();

        $domain = Domain::whereBelongsTo($user)->findOrFail($request->domain_id);
        $domainSetup = DomainSetup::active()->where('extension', '.' . explode('.', @$domain->domain)[1])->with('pricing')->firstOrFail();
        $getRenewPrice = null;

        if (@$domainSetup) {
            $getRenewPrice = @$domainSetup->pricing->renewPrice()[@$request->renew_year];
        }

        if (@$getRenewPrice['price'] < 0) {
            $notify[] = ['error', 'Sorry, Invalid request'];
            return back()->withNotify($notify);
        }

        if (!$domain->domain_register_id) {
            $notify[] = ['error', 'Sorry, There is no register for this domain'];
            return back()->withNotify($notify);
        }

        $cart = ShoppingCart::domainRenew()->whereBelongsTo($user)->where('domain_id', $domain->id)->first();

        if (!$cart) {
            $cart = new ShoppingCart();
        }

        $cart->user_id = $user->id;
        $cart->domain_id = $domain->id;
        $cart->type = 4; //4 means Domain Renew
        /**
         * For knowing about type
         * @see \App\Models\ShoppingCart go to type method
         */
        $cart->domain = $domain->domain;
        $cart->reg_period = @$request->renew_year;
        $cart->price = @$getRenewPrice['renew'] ?? 0;
        $cart->setup_fee = 0;
        $cart->discount = 0;
        $cart->total = ($cart->price + $cart->setup_fee);
        $cart->after_discount = $cart->price;
        $cart->save();

        $this->removeAppliedCoupon();
        return redirect()->route('shopping.cart');
    }

    public function configDomain($id)
    {

        $cart = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->findOrFail($id);

        $domainSetup = DomainSetup::findOrFail($cart->domain_setup_id);

        if (empty($domainSetup->pricing->firstPrice)) {
            return redirect()->route('shopping.cart');
        }

        $pageTitle = 'Domains Configuration';
        return view('Template::user.domain_config', compact('pageTitle', 'domainSetup', 'cart'));
    }

    public function configDomainUpdate(Request $request)
    {

        $request->validate([
            'cart_id' => 'required',
            'domain_setup_id' => 'required',
            'reg_period' => 'required|integer|between:1,6',
            'id_protection' => 'nullable|integer|between:1,6',
        ]);

        $cart = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->findOrFail($request->cart_id);

        $domainSetup = DomainSetup::findOrFail($request->domain_setup_id);

        //Get domain/TLD price for requested year
        $domainRegisterPrice = @$domainSetup->pricing->singlePrice($request->reg_period) ?? 0;

        //Get domain/TLD ID protection for requested year
        $idProtectionPrice = @$domainSetup->pricing->singlePrice($request->id_protection, true) ?? 0;

        $cart->reg_period = $request->reg_period;
        $cart->id_protection = $request->id_protection ? 1 : 0;
        $cart->price = $domainRegisterPrice;
        $cart->setup_fee = $idProtectionPrice;
        $cart->total = ($domainRegisterPrice + $idProtectionPrice);
        $cart->after_discount = $cart->total;

        //If register new/only domain
        if (!$cart->product_id) {
            $cart->ns1 = $request->ns1;
            $cart->ns2 = $request->ns2;
            $cart->ns3 = $request->ns3;
            $cart->ns4 = $request->ns4;
        }

        $cart->save();

        $notify[] = ['success', 'Domains configuration updated successfully'];
        return to_route('shopping.cart')->withNotify($notify);
    }

    public function addService(Request $request)
    {

        $request->validate([
            'product_id' => 'required',
            'billing_cycle' => 'required|in:' . pricing(),
        ]);

        $product = Product::active()
            ->where('id', $request->product_id)
            ->whereHas('price', function ($query) {
                $query->priceFilter();
            })
            ->whereHas('serviceCategory', function ($query) {
                $query->active();
            })
            ->with([
                'getConfigs.activeGroup.activeOptions.activeSubOptions',
                'price'
            ])
            ->firstOrFail([
                'id',
                'category_id',
                'product_type',
                'payment_type',
                'domain_register',
                'stock_control',
                'stock_quantity'
            ]);

        //Checking the product for the service/hosting
        if ($product->price->{$request->billing_cycle} < 0) {
            $notify[] = ['error', 'Sorry, Invalid request'];
            return redirect()->route('shopping.cart')->withNotify($notify);
        }

        if ($product->stock_control && $product->stock_quantity <= 0) {
            $notify[] = ['error', 'Sorry, Out of stock'];
            return back()->withNotify($notify);
        }

        $getSetup = null;

        //If domain required for this product/service
        if ($product->domain_register) {

            $request->validate([
                'domain' => ['regex:/^[a-zA-Z0-9.-]+$/']
            ]);

            $domain = $request->domain;
            $request->merge(['domain' => $domain]);

            // If user registers a new domain
            if ($request->domain_id) {

                $defaultDomainRegister = DomainRegister::getDefault();
                if (!$defaultDomainRegister) {
                    $notify[] = ['info', 'There is no default domain register, Please setup default domain register'];
                    return back()->withNotify($notify);
                }

                $register = new Register($defaultDomainRegister->alias); //The Register is a class
                $register->singleSearch = true;
                $register->command = 'searchDomain';
                $register->domain = $domain;
                $execute = $register->run();

                if (!$execute['success']) {
                    $notify = [];
                    foreach ((array) $execute['message'] as $message) {
                        $notify[] = ['error', $message];
                    }
                    return redirect()->route('register.domain')->withNotify($notify);
                }

                if (@$execute['data']['status'] == 'ERROR') {
                    $notify[] = ['error', $execute['data']['message']];
                    return back()->withNotify($notify);
                }

                $getSetup = $execute;
            }
        }

        //Get product price
        $productPrice = pricing($product->payment_type, $product->price, 'price', false, $request->billing_cycle);

        //Get product setup fee/price
        $productSetup = pricing($product->payment_type, $product->price, 'setupFee', false, $request->billing_cycle);

        //Checking configuration options and their values
        if ($request->config_options) {
            foreach ($request->config_options as $option => $select) {

                if ($option) {
                    $optionResponse = $this->getOptionAndSelect($product, 'option', $option);

                    if (!$optionResponse['success']) {
                        $notify[] = ['error', $optionResponse['message']];
                        return back()->withNotify($notify);
                    }
                }

                if ($select) {
                    $selectResponse = $this->getOptionAndSelect($product, 'select', $select, $request->billing_cycle);

                    $productPrice += @$selectResponse['price'];
                    $productSetup += @$selectResponse['setupFee'];

                    if (!$selectResponse['success']) {
                        $notify[] = ['error', $selectResponse['message']];
                        return back()->withNotify($notify);
                    }
                }
            }
        }

        $billingType = 0;
        if ($product->payment_type != 1) {
            $billingType = billingCycle($request->billing_cycle, true)['index'];
        }

        //Cart update or create new for service/hosting
        $cart = ShoppingCart::where('product_id', $request->product_id)->where('billing_cycle', $billingType)->first();

        if (!$cart) {
            $cart = new ShoppingCart();
        }

        $cart->user_id = $this->userId(); //auth()->user()->id;
        $cart->product_id = $request->product_id;

        $cart->type = $product->domain_register ? 2 : 1;
        $cart->billing_cycle = $billingType;

        $cart->domain = $request->domain;
        $cart->ns1 = $request->ns1;
        $cart->ns2 = $request->ns2;
        $cart->password = $request->password;
        $cart->hostname = $request->hostname;
        $cart->price = $productPrice;
        $cart->setup_fee = $productSetup;
        $cart->discount = 0;
        $cart->total = ($productPrice + $productSetup);
        $cart->after_discount = $cart->total;

        $cart->domain_register_id = @$defaultDomainRegister->id;

        $cart->config_options = @array_filter((array) $request->config_options);
        $cart->save();

        //Remove all coupons
        $this->removeAppliedCoupon();

        //Cart update or create new for domain
        if ($request->domain_id && $request->domain) {

            $cart = ShoppingCart::where('domain_setup_id', $request->domain_id)->where('domain', $request->domain)->where('billing_cycle', $billingType)->first();

            if (!$cart) {
                $cart = new ShoppingCart();
            }

            $cart->user_id = $this->userId();
            $cart->product_id = $request->product_id;
            $cart->domain_setup_id = $request->domain_id;
            $cart->type = 2; //2 means Hosting and Domain both
            /**
             * For knowing about type
             * @see \App\Models\ShoppingCart go to type method
             */
            $cart->billing_cycle = $billingType;
            $cart->reg_period = @$getSetup['setup']->pricing->firstPrice['year'] ?? 0;
            $cart->price = @$getSetup['setup']->pricing->firstPrice['price'] ?? 0;
            $cart->domain = $request->domain;
            $cart->setup_fee = 0;
            $cart->discount = 0;
            $cart->total = ($cart->price + $cart->setup_fee);
            $cart->after_discount = $cart->price;
            $cart->save();

            return redirect()->route('shopping.cart.config.domain', $cart->id);
        }

        return redirect()->route('shopping.cart');
    }

    public function configService($id)
    {

        $cart = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->findOrFail($id);

        $product = Product::where('id', $cart->product_id)
            ->whereHas('price', function ($query) {
                $query->priceFilter();
            })
            ->with([
                'getConfigs.activeGroup.activeOptions.activeSubOptions.getOnlyPrice'
            ])
            ->firstOrFail();

        $domains = [];
        $isUpdate = true;
        $pageTitle = 'Update Product Configure';
        $getBillingCycle = @billingCycle($cart->billing_cycle, true)['billing_cycle'];
        $billingCycle = $getBillingCycle == 'one_time' ? 'monthly' : $getBillingCycle;

        //Checking the product for the service/hosting
        if ($product->price->{$billingCycle} < 0) {
            $notify[] = ['info', 'Something went wrong, Please try again later or delete this item from the cart then try again'];
            return redirect()->route('shopping.cart')->withNotify($notify);
        }

        return view('Template::product_configure', compact('product', 'pageTitle', 'cart', 'billingCycle', 'domains', 'isUpdate'));
    }

    public function configServiceUpdate(Request $request)
    {

        $request->validate([
            'cart_id' => 'required',
            'product_id' => 'required',
            'billing_cycle' => 'required|in:' . pricing(),
        ]);

        $cart = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->findOrFail($request->cart_id);

        //Find the product/service

        $product = Product::active()
            ->where('id', $request->product_id)
            ->whereHas('price', function ($query) {
                $query->priceFilter();
            })
            ->whereHas('serviceCategory', function ($query) {
                $query->active();
            })
            ->with([
                'getConfigs.activeGroup.activeOptions.activeSubOptions',
                'price'
            ])
            ->firstOrFail();

        //Checking the product for the service/hosting
        $getBillingCycle = @billingCycle($cart->billing_cycle, true)['billing_cycle'];
        $billingCycle = $getBillingCycle == 'one_time' ? 'monthly' : $getBillingCycle;

        if ($product->price->{$billingCycle} < 0) {
            $notify[] = ['info', 'Something went wrong, Please try again later or delete this item from the cart then try again'];
            return redirect()->route('shopping.cart')->withNotify($notify);
        }

        //If the product/service is Server/VPS
        if ($product->product_type == 3) {
            $request->validate(['hostname' => 'required', 'password' => 'required', 'ns1' => 'required', 'ns2' => 'required']);
        }

        //Get product price
        $productPrice = pricing($product->payment_type, $product->price, 'price', false, $request->billing_cycle);

        //Get product setup fee/price
        $productSetup = pricing($product->payment_type, $product->price, 'setupFee', false, $request->billing_cycle);

        //Checking configuration options and their values
        if ($request->config_options) {
            foreach ($request->config_options as $option => $select) {

                if ($option) {
                    $optionResponse = $this->getOptionAndSelect($product, 'option', $option);

                    if (!$optionResponse['success']) {
                        $notify[] = ['error', $optionResponse['message']];
                        return back()->withNotify($notify);
                    }
                }

                if ($select) {
                    $selectResponse = $this->getOptionAndSelect($product, 'select', $select, $request->billing_cycle);

                    $productPrice += @$selectResponse['price'];
                    $productSetup += @$selectResponse['setupFee'];

                    if (!$selectResponse['success']) {
                        $notify[] = ['error', $selectResponse['message']];
                        return back()->withNotify($notify);
                    }
                }
            }
        }

        $cart->ns1 = $request->ns1;
        $cart->ns2 = $request->ns2;
        $cart->password = $request->password;
        $cart->hostname = $request->hostname;
        $cart->price = $productPrice;
        $cart->setup_fee = $productSetup;
        $cart->discount = 0;
        $cart->total = ($productPrice + $productSetup);
        $cart->after_discount = $cart->total;
        $cart->config_options = @array_filter((array) $request->config_options);
        $cart->save();

        //Remove all coupons
        $this->removeAppliedCoupon();

        $notify[] = ['success', 'Service configuration updated successfully'];
        return redirect()->route('shopping.cart')->withNotify($notify);
    }

    private function getOptionAndSelect($product, $type, $value, $billing_cycle = null)
    {

        foreach ($product->getConfigs as $config) {
            $options = $config->activeGroup->activeOptions; //Get only active options

            foreach ($options as $option) {
                $subOptions = $option->activeSubOptions; //Get only active sub-options

                if ($type == 'option') {

                    if (!$option->where('status', 1)->find($value)) {
                        return ['success' => false, 'message' => 'The selected option is invalid'];
                    }

                    return ['success' => true, 'data' => $option->find($value)];
                }

                if ($type != 'option') {
                    foreach ($subOptions as $subOption) {

                        if (!$subOption->where('status', 1)->find($value)) {
                            return ['success' => false, 'message' => 'The selected value is invalid'];
                        }

                        $data = $subOption->with('getOnlyPrice')->find($value);
                        $getPrice = pricing($product->payment_type, $data->getOnlyPrice, 'price', false, $billing_cycle);
                        $getSetupFee = pricing($product->payment_type, $data->getOnlyPrice, 'setupFee', false, $billing_cycle);

                        return ['success' => true, 'data' => $data, 'price' => $getPrice, 'setupFee' => $getSetupFee];
                    }
                }
            }
        }
    }

    public function coupon(Request $request)
    {

        $request->validate([
            'coupon_code' => 'required|exists:coupons,code'
        ]);

        $coupon = Coupon::active()->where('code', $request->coupon_code)->first();
        if (!$coupon) {
            $notify[] = ['error', 'The selected coupon code is invalid'];
            return back()->withNotify($notify);
        }

        $carts = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->get();

        foreach ($carts as $cart) {
            $discount = 0;

            if ($coupon && $cart->total >= $coupon->min_order_amount) {
                $discount = $coupon->discount(@$cart->total);

                $cart->coupon_id = $coupon->id;
                $cart->discount = $discount;
                $cart->coupon_discount = $coupon->discount;
                $cart->after_discount = (@$cart->after_discount - $discount);
            }

            $cart->coupon_type = $coupon->type;
            $cart->discount_applied = 1;
            $cart->save();
        }

        $notify[] = ['success', 'Coupon code accepted, Your order total has been updated'];
        return back()->withNotify($notify);
    }

    public function couponRemove()
    {

        $this->removeAppliedCoupon();

        $notify[] = ['success', 'Your order total has been updated'];
        return back()->withNotify($notify);
    }

    private function removeAppliedCoupon()
    {

        $carts = ShoppingCart::where(function ($query) {
            return $this->currentUser($query);
        })->get();

        foreach ($carts as $cart) {
            $cart->discount = 0;
            $cart->coupon_id = 0;
            $cart->discount_applied = 0;
            $cart->coupon_discount = 0;
            $cart->after_discount = $cart->total;
            $cart->save();
        }
    }

    private function currentUser($query)
    {
        return $query->where('user_id', @auth()->user()->id ?? session()->get('randomId'));
    }

    public function userId()
    {
        $user = auth()->user();

        if ($user) {
            return $user->id;
        }

        if (session()->has('randomId')) {
            $randomId = session()->get('randomId');
            session()->put('randomId', $randomId);
            return $randomId;
        }

        $randomId = randomId();
        session()->put('randomId', $randomId);
        return $randomId;
    }
}
