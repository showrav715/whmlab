<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\Domain;
use App\Models\Hosting;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ServiceCategory;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use App\Models\User;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    
    public function dashboard()
    {
        $pageTitle = 'Dashboard';
        $isTenant = isTenant();

        // Add tenant management data for main site
        $tenantData = [];
        if ($isTenant) {
            // Main Site - Add comprehensive tenant management statistics
            try {
                $tenantData['total_tenants'] = Tenant::count();
                // Fix status check - should be 'active' string, not boolean
                $tenantData['active_tenants'] = Tenant::where('data->status', 'active')->count();
                $tenantData['suspended_tenants'] = Tenant::where('data->status', '!=', 'active')->count();
                $tenantData['subscription_plans'] = SubscriptionPlan::count();
                $tenantData['active_subscriptions'] = TenantSubscription::where('status', 'active')->count();
                $tenantData['expired_subscriptions'] = TenantSubscription::where('status', 'expired')->count();
                $tenantData['trial_subscriptions'] = TenantSubscription::where('status', 'trial')->count();
                
                // Recent tenant activities - using raw query to avoid model issues
                $tenantData['recent_tenants'] = collect(DB::table('tenants')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get());
                
                // Monthly tenant growth
                $tenantData['monthly_growth'] = Tenant::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count();
                    
                // Revenue from subscriptions
                $tenantData['subscription_revenue'] = TenantSubscription::where('status', 'active')
                    ->sum('price_paid') ?? 0;
                    
                // Most popular subscription plan
                $popularPlan = TenantSubscription::select('subscription_plan_id', DB::raw('count(*) as total'))
                    ->where('status', 'active')
                    ->groupBy('subscription_plan_id')
                    ->orderByDesc('total')
                    ->first();
                    
                if ($popularPlan && $popularPlan->subscription_plan_id) {
                    $tenantData['popular_plan'] = SubscriptionPlan::find($popularPlan->subscription_plan_id);
                    $tenantData['popular_plan_count'] = $popularPlan->total;
                } else {
                    $tenantData['popular_plan'] = null;
                    $tenantData['popular_plan_count'] = 0;
                }
            } catch (\Exception $e) {
                // Fallback data in case of any database issues
                Log::error('Dashboard tenant data error: ' . $e->getMessage());
                $tenantData = [
                    'total_tenants' => 0,
                    'active_tenants' => 0,
                    'suspended_tenants' => 0,
                    'subscription_plans' => 0,
                    'active_subscriptions' => 0,
                    'expired_subscriptions' => 0,
                    'trial_subscriptions' => 0,
                    'recent_tenants' => collect([]),
                    'monthly_growth' => 0,
                    'subscription_revenue' => 0,
                    'popular_plan' => null,
                    'popular_plan_count' => 0
                ];
            }

            // Main Site - Super Admin Dashboard with Tenant Management Only
            return view('admin.dashboard-super-admin', compact('pageTitle', 'tenantData'));
        } else {
            // Tenant Site - Get business data for tenant operations
            $widget['total_users']             = User::count();
            $widget['verified_users']          = User::active()->count();
            $widget['email_unverified_users']  = User::emailUnverified()->count();
            $widget['mobile_unverified_users'] = User::mobileUnverified()->count();

            $invoiceStatistics = Invoice::selectRaw('
                SUM(CASE WHEN status = 1 THEN amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) as total_unpaid,
                SUM(CASE WHEN status = 3 THEN amount ELSE 0 END) as total_payment_pending,
                SUM(CASE WHEN status = 5 THEN refund_amount ELSE 0 END) as total_refunded,
                COUNT(CASE WHEN status = 2 THEN 0 END) as unpaid
            ')->first(); 

            $orderStatistics = Order::selectRaw(' 
                SUM(after_discount) as total,
                SUM(CASE WHEN status = 1 THEN after_discount ELSE 0 END) as total_active,
                SUM(CASE WHEN status = 2 THEN after_discount ELSE 0 END) as total_pending,
                SUM(CASE WHEN status = 3 THEN after_discount ELSE 0 END) as total_cancelled,
                COUNT(CASE WHEN status = 2 THEN 0 END) as pending
            ')->first(); 

            $statistics['count_active_service'] = Hosting::active()->count();
            $statistics['count_domain_service'] = Domain::active()->count();
            $orderStatus = Order::status();

            // Tenant Site - Limited Dashboard 
            return view('admin.dashboard-tenant', compact('pageTitle', 'widget', 'invoiceStatistics', 'orderStatistics', 'statistics', 'orderStatus'));
        }
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = auth('admin')->user();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        return view('admin.profile', compact('pageTitle', 'admin', 'countries'));
    }

    public function profileUpdate(Request $request)
    {
        $isSuperAdmin = isSuperAdmin();
        $validation = [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])],
        ];

        if($isSuperAdmin){
            $validation['mobile'] = ['required', 'string', 'max:50', 'regex:/^\d{3}\.\d+$/'];
            $validation['country'] = 'required';
        }

        $request->validate($validation);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        if($isSuperAdmin){
            $address = [
                'address' => @$request->address,
                'state' => @$request->state,
                'zip' => @$request->zip,
                'country' => @$request->country,
                'city' => @$request->city,
            ];
    
            $user->mobile = '+'.$request->mobile;
            $user->address = $address;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('admin.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('admin.password')->withNotify($notify);
    }

    public function notifications(){
        $notifications = AdminNotification::orderBy('id','desc')->with('user')->paginate(getPaginate());
        $hasUnread = AdminNotification::where('is_read',Status::NO)->exists();
        $hasNotification = AdminNotification::exists();
        $pageTitle = 'Notifications';
        return view('admin.notifications',compact('pageTitle','notifications','hasUnread','hasNotification'));
    }


    public function notificationRead($id){
        $notification = AdminNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function requestReport()
    {
        $pageTitle = 'Your Listed Report & Request';
        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASECODE');
        $url = "https://license.viserlab.com/issue/get?".http_build_query($arr);
        $response = CurlRequest::curlContent($url);
        $response = json_decode($response);
        if (!$response || !@$response->status || !@$response->message) {
            return to_route('admin.dashboard')->withErrors('Something went wrong');
        }
        if ($response->status == 'error') {
            return to_route('admin.dashboard')->withErrors($response->message);
        }
        $reports = $response->message[0];
        return view('admin.reports',compact('reports','pageTitle'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type'=>'required|in:bug,feature',
            'message'=>'required',
        ]);
        $url = 'https://license.viserlab.com/issue/add';

        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASECODE');
        $arr['req_type'] = $request->type;
        $arr['message'] = $request->message;
        $response = CurlRequest::curlPostContent($url,$arr);
        $response = json_decode($response);
        if (!$response || !@$response->status || !@$response->message) {
            return to_route('admin.dashboard')->withErrors('Something went wrong');
        }
        if ($response->status == 'error') {
            return back()->withErrors($response->message);
        }
        $notify[] = ['success',$response->message];
        return back()->withNotify($notify);
    }

    public function readAllNotification(){
        AdminNotification::where('is_read',Status::NO)->update([
            'is_read'=>Status::YES
        ]);
        $notify[] = ['success','Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function deleteAllNotification(){
        AdminNotification::truncate();
        $notify[] = ['success','Notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteSingleNotification($id){
        AdminNotification::where('id',$id)->delete();
        $notify[] = ['success','Notification deleted successfully'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')).'- attachments.'.$extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error','File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function automationErrors(){
        $pageTitle = 'Automation Errors';
        $notifications = AdminNotification::orderBy('id','desc')->where('api_response', 1)->paginate(getPaginate());
        return view('admin.automation_errors',compact('pageTitle','notifications'));
    }

    public function deleteAutomationErrors(){
  
        AdminNotification::where('api_response', 1)->delete();

        $notify[] = ['success', 'Automation errors deleted successfully'];
        return back()->withNotify($notify);
    }

    public function readAutomationErrors(){

        AdminNotification::where('api_response', 1)->where('is_read', 0)->update([
            'is_read'=>1
        ]);

        $notify[] = ['success', 'Automation errors read successfully'];
        return back()->withNotify($notify);
    }

    public function deleteAutomationError($id){
        $data = AdminNotification::where('api_response', 1)->findOrFail($id);
        $data->delete();

        $notify[] = ['success', 'An automation error was deleted successfully'];
        return back()->withNotify($notify);
    }

    public function checkSlug(Request $request){

    	$validator = Validator::make($request->all(), [
            'input' => 'required',
            'product_id' => 'sometimes|exists:products,id',
            'id' => 'nullable|exists:service_categories,id',
            'model_type' => 'required|in:service_category,product',
            'category_id' => 'sometimes|exists:service_categories,id'
        ]);
     
        if(!$validator->passes()){
            return response()->json(['error'=>$validator->errors()->all()]);
        }
     
        if($request->model_type == 'service_category'){
            $serviceCategory = ServiceCategory::where('slug', $request->input)->when($request->id, function($query) use ($request){
                $query->where('id', '!=', $request->id);
            })->first();

            if($serviceCategory){
                return ['success'=>false, 'message'=>'This slug is already used'];
            }

            return ['success'=>true, 'message'=>'OK'];
        }

        $product = Product::where('slug', $request->input)->where('category_id', $request->category_id)->when($request->product_id, function($query) use ($request){
            $query->where('id', '!=', $request->product_id);
        })->first();
       
        if($product){
            return ['success'=>false, 'message'=>'This slug is already used'];
        }

        return ['success'=>true, 'message'=>'OK'];
    }

    public function orderStatistics(Request $request){

        if ($request->time == 'year') {
            $time = now()->startOfYear();
            $type = 'monthname';
        }
        elseif($request->time == 'month'){
            $time = now()->startOfMonth();
            $type = 'date';
        }
        elseif($request->time == 'week'){
            $time = now()->startOfWeek();
            $type = 'dayname';
        }
        else{
            $time = now()->startOfDay();
            $type = 'hour';
        }

        $orders = Order::query(); 

        $status = $request->status; 
        if($status){
            $orders = $orders->$status();
        }    

        $orders = $orders->where('created_at', '>=', $time)->selectRaw("SUM(amount) as amount, $type(created_at) as date")->groupBy('date')->get();
        $totalOrders = $orders->sum('amount');

        if($type == 'monthname'){
            $orders = $orders->sortBy(function ($item) {
                return Carbon::parse($item['date'])->format('m');
            })->values()->all();
        }
        elseif($request->time == 'week'){
            $orders = $orders->sortBy(function ($item) {
                return Carbon::parse($item['date'])->dayOfWeek;
            })->values()->all();
        }
    
        $orders = collect($orders)->mapWithKeys(function($order) use ($type){  
            $date = $order->date;

            if($type == 'hour'){
                $date = date("g A", mktime($order->date));
            }

            return [
                $date => (float) $order->amount
            ];
        });

        return [
            'orders'=>$orders,
            'total_orders'=>$totalOrders,
        ];
    }
    
    public function services(){
        $pageTitle = 'All Services';
        $services = Hosting::orderBy('id', 'DESC')->searchable(['user:username', 'user:email'])->with('product.serviceCategory', 'user')->paginate(getPaginate());
        return view('admin.services', compact('pageTitle', 'services'));
    }

    public function domains(){
        $pageTitle = 'All Domains';
        $domains = Domain::orderBy('id', 'DESC')->searchable(['user:username', 'user:email', 'domain'])->with('user')->paginate(getPaginate());
        return view('admin.domains', compact('pageTitle', 'domains'));
    }

}
