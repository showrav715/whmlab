<?php

namespace App\Http\Controllers\User;

use App\DomainRegisters\Register;
use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\DomainSetup;
use Illuminate\Http\Request;

class DomainController extends Controller{
    
    public function list(){
        $pageTitle = 'Domain List';
        $domains = Domain::whereBelongsTo(auth()->user())->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.domain.list', compact('pageTitle', 'domains'));
    }

    public function details($id){
        $domain = Domain::whereBelongsTo(auth()->user())->findOrFail($id);
        $pageTitle = 'Domain Details';
        $renewPricing = DomainSetup::active()->where('extension', '.'.explode('.', @$domain->domain)[1])->with('pricing')->first()->pricing ?? [];

        return view('Template::user.domain.details', compact('pageTitle', 'domain', 'renewPricing'));
    }

    public function nameServerUpdate(Request $request){

        $request->validate([
            'domain_id'=>'required|integer',
            'ns1'=>'required',
            'ns2'=>'required'
        ]);

        $domain = Domain::whereBelongsTo(auth()->user())->findOrFail($request->domain_id);
    
        if(!@$domain->register->alias){
            $notify[] = ['error', 'Sorry, Something went wrong'];
            return back()->withNotify($notify);
        }

        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->request = $request;
        $register->command = 'changeNameservers';
        $execute = $register->run();
     
        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'Changed nameservers successfully'];
        return back()->withNotify($notify);
    }

    public function contact($id){
        
        $domain = Domain::whereBelongsTo(auth()->user())->findOrFail($id);
        $pageTitle = 'Contact Information';

        if(!$domain->register){
            $notify[] = ['error', 'Sorry, There is no domain register'];
            return redirect()->route('user.domain.details', $domain->id)->withNotify($notify);
        }

        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->command = 'getContact';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $contactInfo = $execute['response'];
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.domain.contact.'.$domain->register->alias, compact('pageTitle', 'domain', 'countries', 'contactInfo'));
    }

    public function contactUpdate(Request $request){

        $domain = Domain::whereBelongsTo(auth()->user())->findOrFail($request->domain_id); 

        if(!$domain->register){
            $notify[] = ['error', 'Sorry, There is no domain register'];
            return redirect()->route('user.domain.details', $domain->id)->withNotify($notify);
        }

        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->request = $request;
        $register->command = 'setContact';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'The changes to the domain were saved successfully'];
        return back()->withNotify($notify);
    }

}
