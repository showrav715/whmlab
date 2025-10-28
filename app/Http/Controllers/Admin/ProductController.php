<?php

namespace App\Http\Controllers\Admin;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ConfigurableGroup;
use App\Models\ServerGroup;
use App\Models\ServiceCategory;
use App\Models\Pricing;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public function products()
    {
        $pageTitle = 'All Products';

        $groupByCategories = ServiceCategory::with(['products' => function ($product) {
            $product->select('id', 'name', 'category_id', 'product_type', 'payment_type', 'stock_control', 'stock_quantity', 'domain_register', 'status', 'module_option', 'server_group_id');
        }])->get(['id', 'name']);

        return view('admin.product.all', compact('pageTitle', 'groupByCategories'));
    }

    public function addProductPage()
    {
        $pageTitle = 'Add New Product';
        $categories = ServiceCategory::active()->get();

        $serverGroups = ServerGroup::active()->whereHas('servers', function ($server) {
            $server->active($server);
        })->get();

        return view('admin.product.add', compact('pageTitle', 'categories', 'serverGroups'));
    }

    public function addProduct(Request $request)
    {
        if (!preg_match("/^[0-9a-zA-Z-]+$/", $request->slug)) {
            $notify[] = ['error', 'Please provide a valid slug'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'name' => 'required|max:255',
            'product_type' => 'required|integer|between:1,4',
            'service_category' => 'required|exists:service_categories,id',

            'module_option' => 'sometimes|between:1,3',
            'server_group' => $request->server_group ? 'required|exists:server_groups,id' : 'nullable',
            'package_name' => $request->server_group ? 'required' : 'nullable',

            'slug' => [
                'required',
                'max:255',
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->service_category)->where('slug', $request->slug);
                }),
            ],
            'server_id' => 'nullable|exists:servers,id',
        ]);

        $product = new Product();
        $product->module_option = $request->module_option ?? 3;

        $product->package_name = $request->package_name;
        $product->server_id = $request->server_id ?? 0;

        $product->category_id = $request->service_category;
        $product->server_group_id = $request->server_group ?? 0;
        $product->product_type = $request->product_type;

        $product->name = $request->name;
        $product->slug = $request->slug;
        $product->save();

        $pricing = new Pricing();
        $pricing->type = 'product';
        $pricing->product_id = $product->id;

        //Negative one means disable
        $pricing->monthly = -1;
        $pricing->quarterly = -1;
        $pricing->semi_annually = -1;
        $pricing->annually = -1;
        $pricing->biennially = -1;
        $pricing->triennially = -1;
        $pricing->save();

        $notify[] = ['success', 'Product added successfully'];
        return redirect()->route('admin.product.update.page', $product->id)->withNotify($notify);
    }

    public function editProductPage($id)
    {   
        $pageTitle = 'Update Product';

        $product = Product::with('getConfigs')->findOrFail($id);
        $categories = ServiceCategory::where('status', 1)->get();
        $configGroups = ConfigurableGroup::where('status', 1)->orderBy('id', 'DESC')->get();

        $serverGroups = ServerGroup::active()->whereHas('servers', function ($server) {
            $server->active($server);
        })->get();

        $serverGroup = @$product->serverGroup;
        $execute = HostingManager::init($serverGroup)->getPackage($serverGroup);
        $packages = $execute['data'] ?? [];
        
        return view('admin.product.edit', compact('pageTitle', 'categories', 'configGroups', 'serverGroups', 'product', 'packages'));
    }

    public function updateProduct(Request $request)
    {
        if (!preg_match("/^[0-9a-zA-Z-]+$/", $request->slug)) {
            $notify[] = ['error', 'Please provide a valid slug'];
            return back()->withNotify($notify);
        }

        $request->validate([
            'id' => 'required',
            'name' => 'required|max:255',
            'product_type' => 'required|integer|between:1,4',
            'welcome_email' => 'required|integer|between:0,4',
            'service_category' => 'required|exists:service_categories,id',
            'domain_registration' => 'required|integer|in:0,1',
            'stock_control' => 'required|integer|in:0,1',
            'stock_quantity' => 'required_if:stock_control,==,1|integer|gte:0',

            'assigned_config_group' => 'sometimes|array',
            'assigned_config_group.*' => 'sometimes|exists:configurable_groups,id',

            'description' => 'sometimes|max:65000',
            'module_option' => 'sometimes|between:1,3',

            'server_group' => $request->server_group ? 'required|exists:server_groups,id' : 'nullable',
            'package_name' => $request->server_group ? 'required' : 'nullable',

            'slug' => [
                'required',
                'max:255',
                Rule::unique('products')->ignore($request->id)->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->service_category)->where('slug', $request->slug);
                }),
            ],

            'payment_type' => 'required|in:1,2',
            'monthly_setup_fee' => 'required|numeric',
            'monthly' => 'required|numeric',
            'quarterly_setup_fee' => 'required|numeric',
            'quarterly' => 'required|numeric',
            'semi_annually_setup_fee' => 'required|numeric',
            'semi_annually' => 'required|numeric',
            'annually_setup_fee' => 'required|numeric',
            'annually' => 'required|numeric',
            'biennially_setup_fee' => 'required|numeric',
            'biennially' => 'required|numeric',
            'triennially_setup_fee' => 'required|numeric',
            'triennially' => 'required|numeric',
            'server_id' => 'nullable|exists:servers,id',
        ]);

        $product = Product::findOrFail($request->id);
        $product->category_id = $request->service_category;
        $product->payment_type = $request->payment_type;
        $product->product_type = $request->product_type;
        $product->server_group_id = $request->server_group ?? 0;
        $product->module_option = $request->module_option ?? 3;

        $product->server_id = $request->server_id ?? 0;
        $product->package_name = $request->package_name;

        $product->name = $request->name;
        $product->slug = $request->slug;

        $product->welcome_email = $request->welcome_email;
        $product->domain_register = $request->domain_registration;

        $product->stock_control = $request->stock_control;
        $product->stock_quantity = $request->stock_quantity;
        $product->description = $request->description;

        $product->save();

        $product->configures()->sync($request->assigned_config_group);

        //Negative one means disable
        $pricing = $product->price;
        $pricing->monthly_setup_fee = $request->monthly_setup_fee;
        $pricing->monthly = $request->monthly_status ? $request->monthly : -1;

        $pricing->quarterly_setup_fee = $request->quarterly_setup_fee;
        $pricing->quarterly = $request->quarterly_status ? $request->quarterly : -1;

        $pricing->semi_annually_setup_fee = $request->semi_annually_setup_fee;
        $pricing->semi_annually = $request->semi_annually_status ? $request->semi_annually : -1;

        $pricing->annually_setup_fee = $request->annually_setup_fee;
        $pricing->annually = $request->annually_status ? $request->annually : -1;

        $pricing->biennially_setup_fee = $request->biennially_setup_fee;
        $pricing->biennially = $request->biennially_status ? $request->biennially : -1;

        $pricing->triennially_setup_fee = $request->triennially_setup_fee;
        $pricing->triennially = $request->triennially_status ? $request->triennially : -1;
        $pricing->save();

        $notify[] = ['success', 'Product updated successfully'];
        return back()->withNotify($notify);
    }

    public function getServerPackage(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'server_group_id' => 'required|integer'
        ]);

        if (!$validator->passes()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $serverGroup = ServerGroup::find($request->server_group_id);
        if (!$serverGroup) {
            return response()->json(['success' => false, 'message' => 'Sorry, Invalid request']);
        }

        $execute = HostingManager::init($serverGroup)->getPackage($serverGroup);
        return $execute;
    }

    public function status($id)
    {
        return Product::changeStatus($id);
    }
}
