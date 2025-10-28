<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Admin;
use App\Models\BillingSetting;
use App\Models\ConfigurableGroup;
use App\Models\DomainRegister;
use App\Models\DomainSetup;
use App\Models\Frontend;
use App\Models\GatewayCurrency;
use App\Models\Product;
use App\Models\Server;
use App\Models\ServerGroup;
use App\Models\ServiceCategory;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    protected function completed(){
        $completed = [];

        $general = gs();
        $billingSetting = BillingSetting::first();

        if($general->site_name && file_exists(getFilePath('logoIcon').'/logo.png')){
            $completed['name_and_logo'] = 1;
        }

        if(ServiceCategory::first()){
            $completed['service_category'] = 1;
        }

        if(Product::first()){
            $completed['product'] = 1;
        }

        if(ConfigurableGroup::first()){
            $completed['configurable_group'] = 1;
        }

        if(Server::first()){
            $completed['server'] = 1;
        }

        if(ServerGroup::first()){
            $completed['server_group'] = 1;
        }

        if(DomainSetup::first()){
            $completed['domain_setup'] = 1;
        }

        if(DomainRegister::where('setup_done', 1)->first()){
            $completed['domain_register'] = 1;
        }

        if($general->last_cron && Carbon::parse($general->last_cron)->diffInMinutes() < 15){
            $completed['cron'] = 1;
        }

        $array = (array) $billingSetting->create_invoice;

        if($billingSetting->create_default_invoice_days || $billingSetting->create_domain_invoice_days || array_filter($array)){
            $completed['billing_setting'] = 1;
        }

        if(DomainRegister::getDefault()){
            $completed['defaultDomainRegister'] = 1;
        }

        if(GatewayCurrency::first()){
            $completed['setup_gateway'] = 1;
        }

        $admin = Admin::first();
        if($admin->email && $admin->mobile && @$admin->address->address && @$admin->address->state && @$admin->address->zip && @$admin->address->country && @$admin->address->city){
            $completed['admin_profile_setup'] = 1;
        }

        return $completed;
    }

    public function systemSetting(){
        $pageTitle = 'System Settings';
        $settings = json_decode(file_get_contents(resource_path('views/admin/setting/settings.json')));
        $completed = $this->completed();
        return view('admin.setting.system', compact('pageTitle','settings', 'completed'));
    }
    public function general()
    {
        $pageTitle = 'General Setting';
        $timezones = timezone_identifiers_list();
        $currentTimezone = array_search(config('app.timezone'),$timezones);
        return view('admin.setting.general', compact('pageTitle','timezones','currentTimezone'));
    }

    public function generalUpdate(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:40',
            'cur_text' => 'required|string|max:40',
            'cur_sym' => 'required|string|max:40',
            'base_color' => 'nullable|regex:/^[a-f0-9]{6}$/i',
            'timezone' => 'required|integer',
            'currency_format'=>'required|in:1,2,3',
            'paginate_number'=>'required|integer',

            'invoice_start' => 'required|integer|min:1',
            'invoice_increment' => 'required|integer|min:1',
            'tax' => 'required|numeric|gte:0|max:100',
        ]);

        $timezones = timezone_identifiers_list();
        $timezone = @$timezones[$request->timezone] ?? 'UTC';

        $general = gs();
        $general->site_name = $request->site_name;
        $general->cur_text = $request->cur_text;
        $general->cur_sym = $request->cur_sym;
        $general->paginate_number = $request->paginate_number;
        $general->base_color = str_replace('#','',$request->base_color);
        $general->currency_format = $request->currency_format;

        $general->invoice_start = $request->invoice_start;
        $general->invoice_increment = $request->invoice_increment;
        $general->tax = $request->tax;

        $general->save();

        $timezoneFile = config_path('timezone.php');
        $content = '<?php $timezone = "'.$timezone.'" ?>';
        file_put_contents($timezoneFile, $content);
        RequiredConfig::configured('general_setting');
        $notify[] = ['success', 'General setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function systemConfiguration(){
        $pageTitle = 'System Configuration';
        return view('admin.setting.configuration', compact('pageTitle'));
    }


    public function systemConfigurationSubmit(Request $request)
    {
        $general = gs();
        $general->kv = $request->kv ? Status::ENABLE : Status::DISABLE;
        $general->ev = $request->ev ? Status::ENABLE : Status::DISABLE;
        $general->en = $request->en ? Status::ENABLE : Status::DISABLE;
        $general->sv = $request->sv ? Status::ENABLE : Status::DISABLE;
        $general->sn = $request->sn ? Status::ENABLE : Status::DISABLE;
        $general->pn = $request->pn ? Status::ENABLE : Status::DISABLE;
        $general->force_ssl = $request->force_ssl ? Status::ENABLE : Status::DISABLE;
        $general->secure_password = $request->secure_password ? Status::ENABLE : Status::DISABLE;
        $general->registration = $request->registration ? Status::ENABLE : Status::DISABLE;
        $general->agree = $request->agree ? Status::ENABLE : Status::DISABLE;
        $general->multi_language = $request->multi_language ? Status::ENABLE : Status::DISABLE;

        $general->deposit_module = $request->deposit_module ? Status::ENABLE : Status::DISABLE;
        $general->auto_domain_register = $request->auto_domain_register ? Status::ENABLE : Status::DISABLE;

        $general->save();
        $notify[] = ['success', 'System configuration updated successfully'];
        return back()->withNotify($notify);
    }


    public function logoIcon()
    {
        $pageTitle = 'Logo & Favicon';
        return view('admin.setting.logo_icon', compact('pageTitle'));
    }

    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo' => ['image',new FileTypeValidate(['jpg','jpeg','png'])],
            'logo_dark' => ['image',new FileTypeValidate(['jpg','jpeg','png'])],
            'favicon' => ['image',new FileTypeValidate(['png'])],
        ]);
        $path = getFilePath('logoIcon');
        if ($request->hasFile('logo')) {
            try {
                fileUploader($request->logo,$path,filename:'logo.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the logo'];
                return back()->withNotify($notify);
            }
        }
        if ($request->hasFile('logo_dark')) {
            try {
                fileUploader($request->logo_dark,$path,filename:'logo_dark.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the dark logo'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                fileUploader($request->favicon,$path,filename:'favicon.png');
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the favicon'];
                return back()->withNotify($notify);
            }
        }
        RequiredConfig::configured('logo_favicon');
        $notify[] = ['success', 'Logo & favicon updated successfully'];
        return back()->withNotify($notify);
    }

    public function customCss(){
        $pageTitle = 'Custom CSS';
        $file = activeTemplate(true).'css/custom.css';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.custom_css',compact('pageTitle','fileContent'));
    }

    public function sitemap(){
        $pageTitle = 'Sitemap XML';
        $file = 'sitemap.xml';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.sitemap',compact('pageTitle','fileContent'));
    }

    public function sitemapSubmit(Request $request){
        $file = 'sitemap.xml';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file,$request->sitemap);
        $notify[] = ['success','Sitemap updated successfully'];
        return back()->withNotify($notify);
    }



    public function robot(){
        $pageTitle = 'Robots TXT';
        $file = 'robots.xml';
        $fileContent = @file_get_contents($file);
        return view('admin.setting.robots',compact('pageTitle','fileContent'));
    }

    public function robotSubmit(Request $request){
        $file = 'robots.xml';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file,$request->robots);
        $notify[] = ['success','Robots txt updated successfully'];
        return back()->withNotify($notify);
    }


    public function customCssSubmit(Request $request){
        $file = activeTemplate(true).'css/custom.css';
        if (!file_exists($file)) {
            fopen($file, "w");
        }
        file_put_contents($file,$request->css);
        $notify[] = ['success','CSS updated successfully'];
        return back()->withNotify($notify);
    }

    public function maintenanceMode()
    {
        $pageTitle = 'Maintenance Mode';
        $maintenance = Frontend::where('data_keys','maintenance.data')->firstOrFail();
        return view('admin.setting.maintenance',compact('pageTitle','maintenance'));
    }

    public function maintenanceModeSubmit(Request $request)
    {
        $request->validate([
            'description'=>'required',
            'image'=>['nullable',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);
        $general = gs();
        $general->maintenance_mode = $request->status ? Status::ENABLE : Status::DISABLE;
        $general->save();

        $maintenance = Frontend::where('data_keys','maintenance.data')->firstOrFail();
        $image = @$maintenance->data_values->image;
        if ($request->hasFile('image')) {
            try {
                $old = $image;
                $image = fileUploader($request->image, getFilePath('maintenance'), getFileSize('maintenance'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $maintenance->data_values = [
            'description' => $request->description,
            'image'=>$image
        ];
        $maintenance->save();

        $notify[] = ['success','Maintenance mode updated successfully'];
        return back()->withNotify($notify);
    }

    public function cookie(){
        $pageTitle = 'GDPR Cookie';
        $cookie = Frontend::where('data_keys','cookie.data')->firstOrFail();
        return view('admin.setting.cookie',compact('pageTitle','cookie'));
    }

    public function cookieSubmit(Request $request){
        $request->validate([
            'short_desc'=>'required|string|max:255',
            'description'=>'required',
        ]);
        $cookie = Frontend::where('data_keys','cookie.data')->firstOrFail();
        $cookie->data_values = [
            'short_desc' => $request->short_desc,
            'description' => $request->description,
            'status' => $request->status ? Status::ENABLE : Status::DISABLE,
        ];
        $cookie->save();
        $notify[] = ['success','Cookie policy updated successfully'];
        return back()->withNotify($notify);
    }


    public function socialiteCredentials()
    {
        $pageTitle = 'Social Login Credentials';
        return view('admin.setting.social_credential', compact('pageTitle'));
    }

    public function updateSocialiteCredentialStatus($key)
    {
        $general = gs();
        $credentials = $general->socialite_credentials;
        try {
            $credentials->$key->status = $credentials->$key->status == Status::ENABLE ? Status::DISABLE : Status::ENABLE;
        } catch (\Throwable $th) {
            abort(404);
        }

        $general->socialite_credentials = $credentials;
        $general->save();

        $notify[] = ['success', 'Status changed successfully'];
        return back()->withNotify($notify);
    }

    public function updateSocialiteCredential(Request $request, $key)
    {
        $general = gs();
        $credentials = $general->socialite_credentials;
        try {
            @$credentials->$key->client_id = $request->client_id;
            @$credentials->$key->client_secret = $request->client_secret;
        } catch (\Throwable $th) {
            abort(404);
        }
        $general->socialite_credentials = $credentials;
        $general->save();

        $notify[] = ['success', ucfirst($key) . ' credential updated successfully'];
        return back()->withNotify($notify);
    }

}
