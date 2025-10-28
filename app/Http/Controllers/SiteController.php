<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\DomainRegisters\Register;
use App\Models\AdminNotification;
use App\Models\DomainRegister;
use App\Models\DomainSetup;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Subscriber;
use App\Models\Page;
use App\Models\Product;
use App\Models\ServiceCategory;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;


class SiteController extends Controller
{
    public function index(){
        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $pageTitle = 'Home';
        $sections = Page::where('tempname',activeTemplate())->where('slug','/')->first();
        $seoContents = $sections->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::home', compact('pageTitle','sections','seoContents','seoImage'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname',activeTemplate())->where('slug',$slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle','sections','seoContents','seoImage'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        $user = auth()->user();
        $sections = Page::where('tempname',activeTemplate())->where('slug','contact')->first();
        $seoContents = $sections->seo_content;
        $seoImage = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::contact',compact('pageTitle','user','sections','seoContents','seoImage'));
    }


    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if(!verifyCaptcha()){
            $notify[] = ['error','Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new contact message has been submitted';
        $adminNotification->click_url = urlPath('admin.ticket.view',$ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug)
    {   
        $policy = Frontend::where('slug',$slug)->where('data_keys','policy_pages.element')->firstOrFail();
        $pageTitle = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage = @$seoContents->image ? frontendImage('policy_pages',$seoContents->image,getFileSize('seo'),true) : null;
        return view('Template::policy',compact('policy','pageTitle','seoContents','seoImage'));
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return back();
    }

    public function changeCurrency($code = null)
    {
        $currency = \App\Models\Currency::where('code', $code)->where('status', true)->first();
        
        if (!$currency) {
            // If currency not found, use default currency
            $currency = \App\Models\Currency::getDefault();
            $code = $currency->code;
        }
        
        session()->put('user_currency', $code);
        return back();
    }

    public function blogs() {
        $pageTitle = 'Announcements';
        $sections = Page::where('tempname', activeTemplate())->where('slug', 'announcements')->first();
        return view('Template::blogs', compact('pageTitle', 'sections'));
    }

    public function blogDetails($slug){
        $blog = Frontend::where('slug',$slug)->where('data_keys','blog.element')->firstOrFail();
        $pageTitle = $blog->data_values->title;
        $seoContents = $blog->seo_content;
        $seoImage = @$seoContents->image ? frontendImage('blog',$seoContents->image,getFileSize('seo'),true) : null;
        return view('Template::blog_details',compact('blog','pageTitle','seoContents','seoImage'));
    }


    public function cookieAccept(){
        Cookie::queue('gdpr_cookie',gs('site_name') , 43200);
    }

    public function cookiePolicy(){
        $cookieContent = Frontend::where('data_keys','cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE,404);
        $pageTitle = 'Cookie Policy';
        $cookie = Frontend::where('data_keys','cookie.data')->first();
        return view('Template::cookie',compact('pageTitle','cookie'));
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font/solaimanLipi_bold.ttf');
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance()
    {
        $pageTitle = 'Maintenance Mode';
        if(gs('maintenance_mode') == Status::DISABLE){
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys','maintenance.data')->first();
        return view('Template::maintenance',compact('pageTitle','maintenance'));
    }

    public function registerDomain(Request $request) {

        $pageTitle = 'Register New Domain';
        $domain = strtolower($request->domain);
        $result = [];   

        if ($domain) { 
            $request->validate([
                'domain'=> ['regex:/^[a-zA-Z0-9.-]+$/']
            ]);

            $defaultDomainRegister = DomainRegister::getDefault();
            if (!$defaultDomainRegister) {
                $notify[] = ['info', 'There is no default domain register, please setup default domain register'];
                return redirect()->route('register.domain')->withNotify($notify);
            }
            $request->merge(['domain'=>$domain]);

            $register = new Register($defaultDomainRegister->alias); //The Register is a class
            $register->command = 'searchDomain';
            $register->domain = $domain;
            $execute = $register->run();

            if (!$execute['success']) {
                $notify = [];
                foreach((array) $execute['message'] as $message){
                    $notify[] = ['error', $message];
                }
                return redirect()->route('register.domain')->withNotify($notify);
            }

            if (@$execute['data']['status'] == 'ERROR') {
                $notify[] = ['error', $execute['data']['message']];
                return redirect()->route('register.domain')->withNotify($notify);
            }

            $result = $execute;
        }

        return view('Template::register_domain', compact('pageTitle', 'result'));
    }

    public function serviceCategory($slug = null) {

        $serviceCategory = ServiceCategory::active()->when($slug, function ($category) use ($slug) {
            $category->where('slug', $slug);
        })->firstOrFail(); 

        $pageTitle = $serviceCategory->name;
        return view('Template::service_category', compact('pageTitle', 'serviceCategory'));
    }

    public function productConfigure($categorySlug, $productSlug, $id) {

        $product = Product::active()->where('id', $id)->whereHas('serviceCategory', function ($category) {
            $category->active($category);
        })->whereHas('price', function ($price) {
            $price->priceFilter($price);
        })->with('getConfigs.activeGroup.activeOptions.activeSubOptions.getOnlyPrice')->firstOrFail();

        $domains = [];
        $pageTitle = 'Product Configure';

        if ($product->domain_register) {
            $domains = DomainSetup::active()->orderBy('id', 'DESC')->with('pricing')->get();
        }

        return view('Template::product_configure', compact('product', 'pageTitle', 'domains'));
    } 

    public function subscribe(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:subscribers,email'
        ]);

        if (!$validator->passes()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        $newSubscriber = new Subscriber();
        $newSubscriber->email = $request->email;
        $newSubscriber->save();

        return response()->json(['success' => true, 'message' => 'Thank you, we will notice you our latest news']);
    }

    public function searchDomain(Request $request) {

        $validator = Validator::make($request->all(), [
            'domain' => ['required', 'regex:/^[a-zA-Z0-9.-]+$/']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>false,
                'message'=>[$validator->errors()->all()],
            ]);
        }

        $domain = strtolower($request->domain);
        $request->merge(['domain'=> $domain]);

        $defaultDomainRegister = DomainRegister::getDefault();
        if (!$defaultDomainRegister) {
            return ['success' => false, 'message' => 'There is no default domain register, Please setup default domain register'];
        }

        $register = new Register($defaultDomainRegister->alias); //The Register is a class
        $register->command = 'searchDomain';
        $register->domain = $domain;
        $execute = $register->run();

        if (!$execute['success']) {
            return ['success' => false, 'message' => $execute['message']];
        }

        if (@$execute['data']['status'] == 'ERROR') {
            return ['success' => false, 'message' => $execute['data']['message']];
        }
  
        return ['success' =>true, 'result'=>$execute];
    }
}
