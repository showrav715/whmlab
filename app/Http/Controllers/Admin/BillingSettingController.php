<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BillingSetting;

class BillingSettingController extends Controller{
    
    public function index(){
        $setting = BillingSetting::first();
        $pageTitle = 'Billing Setting';
        return view('admin.setting.billing_setting', compact('pageTitle', 'setting'));
    } 

    public function update(Request $request){

        $request->validate([
            'create_default_invoice_days'=>'required|integer|gte:0',
            'invoice_send_reminder_days'=>'required|integer|gte:0',
            'invoice_first_over_due_reminder'=>'required|integer|gte:0',
            'invoice_second_over_due_reminder'=>'required|integer|gte:0',
            'invoice_third_over_due_reminder'=>'required|integer|gte:0',
            'late_fee_days'=>'required|integer|gte:0',
            'invoice_late_fee_amount'=>'required|numeric|gte:0',
            'invoice_late_fee_type'=>'required|integer|in:1,2',
        ]);

        $billingSetting = BillingSetting::first();
        $billingSetting->create_default_invoice_days = $request->create_default_invoice_days; //Default day for create a new invoice from cron jobs
        $billingSetting->invoice_send_reminder_days = $request->invoice_send_reminder_days; //For unpaid invoice reminder
        $billingSetting->invoice_first_over_due_reminder = $request->invoice_first_over_due_reminder;
        $billingSetting->invoice_second_over_due_reminder = $request->invoice_second_over_due_reminder;
        $billingSetting->invoice_third_over_due_reminder = $request->invoice_third_over_due_reminder;

        $billingSetting->late_fee_days = $request->late_fee_days;
        $billingSetting->invoice_late_fee_amount = $request->invoice_late_fee_amount;
        $billingSetting->invoice_late_fee_type = $request->invoice_late_fee_type; //1=> Fixed, 2=> %

        $billingSetting->invoice_send_reminder = $request->invoice_send_reminder ? 1 : 0; //Enable / Disable

        $billingSetting->save();

        $notify[] = ['success', 'Billing setting updated successfully'];
        return back()->withNotify($notify);
    }

    public function advanceBillingSetting(Request $request){
 
        $request->validate([
            'monthly'=> 'required|integer|gte:0',
            'quarterly'=> 'required|integer|gte:0',
            'semi_annually'=> 'required|integer|gte:0',
            'annually'=> 'required|integer|gte:0',
            'biennially'=> 'required|integer|gte:0',
            'triennially'=> 'required|integer|gte:0',
            'create_domain_invoice_days'=> 'required|integer|gte:0',
        ]);

        $billingSetting = BillingSetting::first();
        $data = $billingSetting->create_invoice;

        array_walk($data, function(&$field, $value) use ($request){
            $field = @$request->input(@$value) ?? "0"; //Days for create a new invoice using billing cycle	
        });

        $billingSetting->create_invoice = $data;

        $billingSetting->create_domain_invoice_days = $request->create_domain_invoice_days; //Day to create a new invoice for domain
        $billingSetting->save();

        $notify[] = ['success', 'Advance billing setting updated successfully'];
        return back()->withNotify($notify);
    }


}


