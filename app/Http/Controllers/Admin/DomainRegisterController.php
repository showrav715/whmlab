<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainRegister;
use App\Models\GeneralSetting;

class DomainRegisterController extends Controller{
      
    public function all(){
        $pageTitle = 'All Domain Registers';
        $domainRegisters = DomainRegister::orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.tld.register', compact('pageTitle', 'domainRegisters'));
    }

    public function update(Request $request){
     
        $register = DomainRegister::findOrFail($request->id);
        $data = (array) $register->params;
        $arrayFields = $data;

        array_walk($arrayFields, function(&$field) use ($request){
       
            if($request->input('test_mode')){ //For test mode validation
                if(@$field->test_mode || @$field->required){
                    $field = 'required'; 
                }
                else{
                    $field = 'nullable';
                }
            }else{  //For production mode validation
                if(!@$field->test_mode || @$field->required){
                    $field = 'required'; 
                }
                else{
                    $field = 'nullable';
                }
            }
            
        });

        $dnsValidation = [
            'ns1'=>'required',
            'ns2'=>'required',
        ];

        $validationRule = array_merge($arrayFields, $dnsValidation);
        $request->validate($validationRule);
   
        array_walk($data, function(&$field, $value) use ($request){ //Assigning value
            $field->value = $request->input($value) ?? ""; 
        });

        $register->test_mode = $request->test_mode ? 1 : 0;
        $register->default = $request->default ? 1 : 0;
        $register->params = $data;

        $register->ns1 = $request->ns1;
        $register->ns2 = $request->ns2;
        $register->ns3 = $request->ns3;
        $register->ns4 = $request->ns4;

        $register->setup_done = 1;
        $register->save();

        if($request->default){
            DomainRegister::where('id', '!=', $request->id)->where('default', 1)->update(['default'=>0]);
        }

        $notify[] = ['success', $register->name.' has been updated'];
        return back()->withNotify($notify);
    }

    public function autoRegister(){
        $generalSetting = GeneralSetting::first();

        if($generalSetting->auto_domain_register){
            $generalSetting->auto_domain_register = 0;
            $message =  'Auto domain register has been turned off successfully';
        }else{
            $generalSetting->auto_domain_register = 1;
            $message =  'Auto domain register has been turned on successfully';
        }

        $generalSetting->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id){
        return DomainRegister::changeStatus($id);
    }

}





 