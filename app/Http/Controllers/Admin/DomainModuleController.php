<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Domain;
use Illuminate\Http\Request;
use App\Models\BillingSetting;
use App\DomainRegisters\Register;
use App\Http\Controllers\Controller;
use App\Lib\SendServiceEmail;

class DomainModuleController extends Controller{
    
    public function moduleCommand(Request $request){

        $request->validate([
            'domain_id'=> 'required',
            'module_type'=> 'required|numeric|between:1,6',
            'ns1'=> 'required_if:module_type,==,1',
            'ns2'=> 'required_if:module_type,==,1',
        ]);
 
        $domain = Domain::findOrFail($request->domain_id);

        if(!$domain->domain_register_id){
            $notify[] = ['error', 'Select register before running the module command'];
            return back()->withNotify($notify);
        }

        if($request->module_type == 1){ 
            return $this->register($domain, $request);
        }
        elseif($request->module_type == 2){
            return $this->changeNameservers($domain);
        }
        elseif($request->module_type == 3){
            return $this->renew($domain);
        }
        elseif($request->module_type == 4){
            return $this->setContact($domain, $request);
        }
        elseif($request->module_type == 5){
            return $this->enableIdProtection($domain);
        }
        elseif($request->module_type == 6){
            return $this->disableIdProtection($domain);
        }

    } 

    protected function register($domain, $request){

        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->request = $request;
        $register->command = 'register';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
 
        if($request->send_email){
            SendServiceEmail::domainNotify($domain);
        }
        /**
        * For knowing about status 
        * @see \App\Models\Domain go to status method 
        */
        $domain->status = 1; //1 means Active 
        $domain->save(); 

        $notify[] = ['success', 'Register module command run successfully'];
        return back()->withNotify($notify);
    } 

    protected function renew($domain){ 
        
        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->command = 'renew';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $billingSetting = BillingSetting::first();
        $domain->next_due_date = Carbon::parse($domain->next_due_date)->addYears($domain->reg_period)->toDateTimeString();

        if(@$billingSetting->create_domain_invoice_days){
            $days = @$billingSetting->create_domain_invoice_days;
        }else{
            $days = $billingSetting->create_default_invoice_days;
        }

        $domain->next_invoice_date = Carbon::parse($domain->next_due_date)->subDays($days)->toDateTimeString();
        $domain->expiry_date = $domain->next_due_date;
        $domain->save();

        $notify[] = ['success', 'Renew module command run successfully for '.$domain->reg_period.' year'];
        return back()->withNotify($notify);
    }

    public function setContact($domain, $request){

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

    protected function changeNameservers($domain){

        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->command = 'changeNameservers';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $notify[] = ['success', 'Change nameservers module command run successfully'];
        return back()->withNotify($notify);
    }

    protected function enableIdProtection($domain){

        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->command = 'enableIdProtection';
        $execute = $register->run();
        
        if(!$execute['success']){
            foreach([$execute['message']] ?? [] as $message){
                $notify[] = ['error', $message];
            }
            return back()->withNotify($notify);
        }
     
        $domain->id_protection = 1;
        $domain->save();

        $notify[] = ['success', 'Domain privacy has been enabled'];
        return back()->withNotify($notify);
    }

    protected function disableIdProtection($domain){

        $register = new Register($domain->register->alias); //The Register is a class
        $register->domain = $domain;
        $register->command = 'disableIdProtection';
        $execute = $register->run();

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
     
        $domain->id_protection = 0;
        $domain->save();

        $notify[] = ['success', 'Domain privacy has been disabled'];
        return back()->withNotify($notify);
    }

    public function domainContact($id){

        $domain = Domain::findOrFail($id);
        
        if(!$domain->register){
            $notify[] = ['error', 'Sorry, There is no domain register'];
            return back()->withNotify($notify);
        }
        
        $pageTitle = 'Domain Contact Information';
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

        return view('admin.service.domain_contact.'.$domain->register->alias, compact('pageTitle', 'domain', 'contactInfo', 'countries'));
    } 


}
