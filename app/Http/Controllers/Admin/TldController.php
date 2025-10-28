<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DomainSetup;
use App\Models\DomainPricing;

class TldController extends Controller{
 
    public function all(){
        $pageTitle = 'All Domains/TLD - Top Level Domain';
        $domains = DomainSetup::orderBy('id', 'DESC')->with('pricing')->paginate(getPaginate());
        return view('admin.tld.all', compact('pageTitle', 'domains'));
    }

    public function add(Request $request){

        $request->validate([
            'extension'=>'required' //Extension means TLD => Top level domain like .com/com
        ]);

        $extension = $request->extension;

        if(substr($extension, 0, 1) != '.'){
            $extension = '.'.$extension;
        }
       
        if(DomainSetup::where('extension', $extension)->exists()){
            $notify[] = ['error', 'The extension has already been taken'];
            return back()->withNotify($notify);
        } 

        $domain = new DomainSetup();
        $domain->extension = $extension;
        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->save();

        $domainPricing = new DomainPricing();
        $domainPricing->domain_id = $domain->id;
        $domainPricing->save();

        $notify[] = ['success', 'Domain extension added successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request){
    
        $request->validate([
            'id'=>'required',
            'extension'=>'required' //Extension means TLD => Top level domain like .com/com
        ]);
  
        $extension = $request->extension;

        if(substr($extension, 0, 1) != '.'){
            $extension = '.'.$extension;
        }

        if(DomainSetup::where('extension', $extension)->where('id', '!=', $request->id)->exists()){
            $notify[] = ['error', 'The extension has already been taken'];
            return back()->withNotify($notify);
        } 

        $domain = DomainSetup::findOrFail($request->id);
        $domain->extension = $extension;
        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->save();

        $notify[] = ['success', 'Domain extension updated successfully'];
        return back()->withNotify($notify);
    }

    public function updatePricing(Request $request){

        $request->validate([
            'id'=>'required|integer',

            'one_year_price'=>'required|numeric',
            'one_year_id_protection'=>'required|numeric',
            'one_year_renew'=>'required|numeric',

            'two_year_price'=>'required|numeric',
            'two_year_id_protection'=>'required|numeric',
            'two_year_renew'=>'required|numeric',

            'three_year_price'=>'required|numeric',
            'three_year_id_protection'=>'required|numeric',
            'three_year_renew'=>'required|numeric',

            'four_year_price'=>'required|numeric',
            'four_year_id_protection'=>'required|numeric',
            'four_year_renew'=>'required|numeric',

            'five_year_price'=>'required|numeric',
            'five_year_id_protection'=>'required|numeric',
            'five_year_renew'=>'required|numeric',

            'six_year_price'=>'required|numeric',
            'six_year_id_protection'=>'required|numeric',
            'six_year_renew'=>'required|numeric',
        ]);

        //Minimum one price required for the TLD / Domain
        if($request->one_year_price < 0 && $request->two_year_price < 0 && 
           $request->three_year_price < 0 && $request->four_year_price < 0 && 
           $request->five_year_price < 0 && $request->six_year_price < 0)
        {
            $notify[] = ['error', 'Minimum one price required'];
            return back()->withNotify($notify);
        }

        $pricing = DomainPricing::findOrFail($request->id);

        $pricing->one_year_price = $request->one_year_price;
        $pricing->one_year_id_protection = $request->one_year_id_protection;
        $pricing->one_year_renew = $request->one_year_renew;

        $pricing->two_year_price = $request->two_year_price;
        $pricing->two_year_id_protection = $request->two_year_id_protection;
        $pricing->two_year_renew = $request->two_year_renew;

        $pricing->three_year_price = $request->three_year_price;
        $pricing->three_year_id_protection = $request->three_year_id_protection;
        $pricing->three_year_renew = $request->three_year_renew;

        $pricing->four_year_price = $request->four_year_price;
        $pricing->four_year_id_protection = $request->four_year_id_protection;
        $pricing->four_year_renew = $request->four_year_renew;

        $pricing->five_year_price = $request->five_year_price;
        $pricing->five_year_id_protection = $request->five_year_id_protection;
        $pricing->five_year_renew = $request->five_year_renew;

        $pricing->six_year_price = $request->six_year_price;
        $pricing->six_year_id_protection = $request->six_year_id_protection;
        $pricing->six_year_renew = $request->six_year_renew;

        $pricing->save();

        $notify[] = ['success', 'Domain pricing updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id){
        return DomainSetup::changeStatus($id);
    }

}
