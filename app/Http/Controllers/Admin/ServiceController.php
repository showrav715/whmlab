<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting; 
use App\Models\HostingConfig; 
use App\Models\Domain; 
use App\Models\ServiceCategory; 
use App\Models\Product;  
use App\Models\DomainRegister;  
use App\HostingModule\HostingManager;

class ServiceController extends Controller{

    public function hostingDetails($id){ 

        $pageTitle = 'Hosting Details';
        $productDropdown = $this->productDropdown(); //Making product dropdown under the categories
        $hosting = Hosting::with('hostingConfigs.select', 'hostingConfigs.option', 'product.getConfigs.group.activeOptions.activeSubOptions.getOnlyPrice')->findOrFail($id);
      
        $server = $hosting->server;
        $serverGroup = @$server->group;
   
        $execute = HostingManager::init($serverGroup)->accountSummary($hosting);
        $accountSummary = @$execute['processed_data'];
        $product = $hosting->product; 
        $hasAccount = @$execute['raw_data'];

        return view('admin.service.hosting_details', compact('pageTitle', 'hosting', 'productDropdown', 'accountSummary', 'serverGroup', 'execute', 'product', 'hasAccount'));
    }    
    
    public function hostingUpdate(Request $request){
        
        /**
        * For knowing about status 
        * @see \App\Models\Hosting go to status method 
        */
        $request->validate([
            'id'=>'required|integer', 
            'status'=>'required|integer|in:'.Hosting::status(true), 
            'domain'=>'required|regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i',
            'server_id'=>'nullable|exists:servers,id',
            'next_invoice_date'=>'required|date_format:d-m-Y',
            'next_due_date'=>'required|date_format:d-m-Y',
            'termination_date'=>'nullable|date_format:d-m-Y',
            'reg_date'=>'required|date_format:d-m-Y',
            'billing_cycle'=>'required|integer|between:0,6',
            'first_payment_amount'=>'required|numeric|gte:0',
            'recurring_amount'=>'required|numeric|gte:0',
        ]);

        $service = Hosting::findOrFail($request->id);
        $product = $service->product;

        $server = $service->server;
        $serverGroup = @$server->group; 

        if($service->server_id != $request->server_id){
            $execute = HostingManager::init($serverGroup)->accountSummary($service);
            if(@$execute['raw_data']){
                $notify[] = ['error', 'Already '.@$serverGroup->getType.' account exists on this hosting. Please terminate the account first.'];
                return back()->withNotify($notify);
            }
        }

        $service->domain = $request->domain;
        $service->first_payment_amount = $request->first_payment_amount;
        $service->recurring_amount = $request->recurring_amount;
        $service->next_due_date = $request->next_due_date;
        $service->next_invoice_date = $request->next_invoice_date;
        $service->billing_cycle = $request->billing_cycle;

        $service->server_id = $request->server_id; 
        
        $service->termination_date = $request->termination_date; 
        $service->admin_notes = $request->admin_notes; 

        $service->dedicated_ip = $request->dedicated_ip; 
        $service->username = $request->username;
        $service->password = str_replace('#', '', $request->password);

        $service->status = $request->status;        
        /**
        * For knowing about status 
        * @see \App\Models\Hosting go to type status 
        */
        $service->reg_date = $request->reg_date;

        //Check configuration options when config_options is found from the request 
        if($request->config_options){
            foreach($request->config_options as $option => $select){
                
                if($option){ //Option validation
                    $optionResponse = $this->getOptionAndSelect($product, 'option', $option);
                    
                    if(!@$optionResponse['success']){
                        $notify[] = ['error', @$optionResponse['message']];
                        return back()->withNotify($notify);
                    } 
                } 
            
                if($select){ //Select validation
                    $selectResponse = $this->getOptionAndSelect($product, 'select', $select); 
                   
                    if(!@$selectResponse['success']){
                        $notify[] = ['error', @$selectResponse['message']];
                        return back()->withNotify($notify);
                    }
                }
                
                //Create or update configuration options
                $exists = HostingConfig::where('hosting_id', $service->id)->where('configurable_group_option_id', $option)->first();
              
                if($select){
                    
                    if($exists){
                        $exists->update(['configurable_group_sub_option_id'=>$select]);
                    }else{
                        $new = new HostingConfig();
                        $new->hosting_id = $service->id;
                        $new->configurable_group_option_id = $option;
                        $new->configurable_group_sub_option_id = $select;
                        $new->save();
                    }
                }elseif(!$select && $exists){
                    $exists->delete();
                }
       
            }
        }

        if($product->product_type == 3){ //3 means Server/VPS
            $service->assigned_ips = $request->assigned_ips;
            $service->ns1 = $request->ns1;
            $service->ns2 = $request->ns2;
        }

        if($request->status == 5 && @$service->cancelRequest->status == 2){ //5 means Cancelled and 2 means Pending
            $cancel = @$service->cancelRequest; 
            $cancel->status = 1;
            $cancel->save();
        }

        if($request->status != 5 && @$service->cancelRequest->status == 1){ //5 means Cancelled and 1 means Completed
            $cancel = @$service->cancelRequest; 
            $cancel->status = 2;
            $cancel->save();
        }

        //When the admin wants to delete the cancel request
        if(@$request->delete_cancel_request){
            @$service->cancelRequest->delete();  
        }

        $service->save();

        $notify[] = ['success', 'Hosting details updated successfully'];
        return back()->withNotify($notify);
    } 

    public function domainDetails($id){   
        $domain = Domain::findOrFail($id);
        $pageTitle = 'Domain Details';
        $domainRegisters = DomainRegister::active()->orderBy('id', 'DESC')->get(['id', 'name']); 
        return view('admin.service.domain_details', compact('pageTitle', 'domain', 'domainRegisters'));
    }  
 
    public function domainUpdate(Request $request){ 
        /**
        * For knowing about status 
        * @see \App\Models\Domain go to status method 
        */
        $request->validate([ 
            'id'=>'required|integer' , 
            'status'=>'required|integer|in:'.Domain::status(true), 
            'domain'=>'required|regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i',
            'reg_date'=>'required|date_format:d-m-Y',
            'next_due_date'=>'required|date_format:d-m-Y',
            'next_invoice_date'=>'required|date_format:d-m-Y',
            'expiry_date'=>'required|date_format:d-m-Y',
            'register_id'=>'exists:domain_registers,id|nullable',
        ]); 

        $domain = Domain::findOrFail($request->id);
        $domain->domain_register_id = $request->register_id;
        $domain->reg_date = $request->reg_date;
        $domain->reg_period = $request->reg_period;
        $domain->next_due_date = $request->next_due_date;
        $domain->next_invoice_date = $request->next_invoice_date;
        $domain->domain = $request->domain;
        $domain->expiry_date = $request->expiry_date;
        $domain->first_payment_amount = $request->first_payment_amount;
        $domain->recurring_amount = $request->recurring_amount;
        
        $domain->ns1 = $request->ns1;
        $domain->ns2 = $request->ns2;
        $domain->ns3 = $request->ns3;
        $domain->ns4 = $request->ns4;
        $domain->admin_notes = $request->admin_notes;

        $domain->id_protection = $request->id_protection ? 1 : 0;
        $domain->status = $request->status;
        /**
        * For knowing about status 
        * @see \App\Models\Domain go to type status 
        */
        $domain->save();

        $notify[] = ['success', 'Domain details updated successfully'];
        return back()->withNotify($notify);
    }

    public function changeHostingProduct($hostingId, $productId){
   
        $product = Product::findOrFail($productId);
        $hosting = Hosting::findOrFail($hostingId);

        $server = $hosting->server;
        $serverGroup = @$server->group; 

        $execute = HostingManager::init($serverGroup)->accountSummary($hosting);
        if(@$execute['raw_data']){
            $notify[] = ['error', 'Already '.@$serverGroup->getType.' account exists on this hosting. Please terminate the account first.'];
            return back()->withNotify($notify);
        }

        $hosting->product_id = $productId;
        $hosting->server_id = $product->server_id;
        $hosting->save();

        $notify[] = ['success', 'Your changes saved successfully'];
        return back()->withNotify($notify);
    }

    protected function productDropdown(){
       
        $option = null;
        $allCategory = ServiceCategory::whereHas('products')->with(['products'=>function($product){
            $product->select('id', 'category_id', 'name');
        }])->get(['id', 'name']);
    
        foreach($allCategory as $category){
            $option .= "<option value='' class='fw-bold'>".trans($category->name)."</option>";

            if(count($category->products)){
                foreach($category->products as $product){
                    $option .= "<option value='$product->id'>&nbsp;&nbsp;&nbsp;".trans($product->name)."</option>";
                }
            }
        }
        
        return $option;
    }

    protected function getOptionAndSelect($product, $type, $value){
        
        foreach($product->getConfigs as $config){
            $options = $config->group->options;

            foreach($options as $option){
                $subOptions = $option->subOptions;
              
                if($type == 'option'){
                
                    if(!$option->find($value)){
                        return ['success'=>false, 'message'=>'The selected option is invalid'];
                    }
         
                }
 
                if($type != 'option'){
                    foreach($subOptions as $subOption){
                        
                        if(!$subOption->find($value)){
                            return ['success'=>false, 'message'=>'The selected value is invalid'];
                        }
                        
                    }
                }

                return ['success'=>true];
            }

        }

    } 

}


