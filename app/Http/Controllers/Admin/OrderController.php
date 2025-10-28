<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Hosting;
use App\Models\Domain;
use App\Models\DomainRegister; 
use App\DomainRegisters\Register;
use App\HostingModule\HostingManager;
use App\Lib\AfterPayment;
use App\Lib\SendServiceEmail;

class OrderController extends Controller{

    protected function with($with = []){
        $array = ['invoice.payments.gateway', 'user'];
        return array_merge($array, $with);
    } 

    public function orders(){ 
        $pageTitle = 'All Orders';
        $orders = $this->orderData();
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function pending(){  
        $pageTitle = 'Pending Orders';
        $orders = $this->orderData('pending');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function active(){  
        $pageTitle = 'Active Orders'; 
        $orders = $this->orderData('active');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function cancelled(){  
        $pageTitle = 'Cancelled Orders';
        $orders = $this->orderData('cancelled');
        return view('admin.order.all', compact('pageTitle', 'orders'));
    }

    public function details($id){    
        $order = Order::with($this->with(['hostings.product.serviceCategory', 'domains.details', 'hostings.details', 'hostings.product.serverGroup']))->findOrFail($id);
        $pageTitle = 'Order Details'; 
        $domainRegisters = DomainRegister::active()->get(['id', 'name']);
        return view('admin.order.details', compact('pageTitle', 'order', 'domainRegisters'));
    }

    public function accept(Request $request){

        $request->validate([
            'order_id'=> 'required|integer'
        ]);

        $order = Order::pending()->findOrFail($request->order_id);
        $error = false; 
        /**
        * This is an array of hosting from request
        * For hosting/service operation
        */
        foreach($request->hostings ?? [] as $id => $hosting){
         
            $hosting = (object) $hosting;
            $service = $order->hostings->find($id); //Finding the service/hosting
          
            if($service){
                $product = $service->product; //Finding the product for the service/hosting
                
                $service->username = @$hosting->username;
                $service->password = @$hosting->password;
                $service->server_id = @$hosting->server_id;
                $service->save();
                /**
                * For knowing about status 
                * @see \App\Models\Hosting go to status method 
                * @see \App\Models\Order go to status method 
                */
                if(@$service->status == 2 && $order->status == 2){ //2 means Pending for both
                    if(@$hosting->run_create_module && @$product->process() != 'manual'){ //If accept the order and run create module 
                
                        $server = $service->server;
                        $serverGroup = @$server->group;
                        $execute = HostingManager::init($serverGroup)->create($service);
            
                        if(!$execute['success']){
                            $error = true;
                            $notify[] = ['error', $execute['message']];
                        }else{
                            $service->status = 1; //1 means Active
                            $service->save();
 
                            if(@$hosting->send_email){
                                SendServiceEmail::serviceNotify($service);
                            }
                        }
                        
                    }
                    elseif(!@$hosting->run_create_module){ //If doesn't accept the order
                        $service->status = 1; //1 means Active
                        $service->save();

                        if(@$hosting->send_email){
                            SendServiceEmail::serviceNotify($service);
                        }
                    }
                }

            }

        }

        /**
        * This is an array of domain from request
        * For domain operation
        */
        foreach($request->domains ?? [] as $id => $domain){

            $domain = (object) $domain;
            $service = $order->domains->find($id); //Finding the domain
            
            /**
            * For knowing about status 
            * @see \App\Models\Domain go to status method 
            * @see \App\Models\Order go to status method 
            */

            if($service && DomainRegister::where('id', $domain->register)->exists()){
                $service->domain_register_id = $domain->register;
                $service->save();
    
                if(@$domain->domain_register){ //If accept the order and run register module 
                    $register = new Register($service->register->alias); //The Register is a class
                    $register->domain = $service;
                    $register->command = 'register';
                    $execute = $register->run();

                    if(!$execute['success']){ 
                        $error = true;
                        $notify[] = ['error', $execute['message']];
                    }else{
                        $service->status = 1; //1 means Active
                        $service->save(); 

                        if(@$domain->send_email){
                            SendServiceEmail::domainNotify($service);
                        }
                    }
                }else{
                    $service->status = 1; //1 means Active
                    $service->save();

                    if(@$domain->send_email){
                        SendServiceEmail::domainNotify($service);
                    }
                }
    
            }

        }

        //Finding the renewed records of the domain from the order and item_type 1 means Domain
        $invoice = $order->invoice;
        foreach(@$invoice->items()->where('item_type', 1)->where('next_due_date', '!=', null)->groupBy('domain_id')->cursor() as $item){

            $domain = $item->domain;

            $renewProcess = new AfterPayment(); //The AfterPayment is a class
            $response = $renewProcess->domainRenewProcessing($domain, $item);

            if(!$response['success']){
                $error = true;
                $notify[] = ['error', $response['message']];
            }else{
                $domain->status = 1; //1 means Active  
                $domain->save();
            }

        }  

        if(!$error){
            $order->status = 1; 
            $order->save();
            $notify[] = ['success', 'Order accepted successfully'];
        }

        return back()->withNotify($notify);
    }

    public function orderNotes(Request $request){

        $request->validate([
            'order_id'=> 'required|integer', 
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->admin_notes = $request->admin_notes;
        $order->save();

        $notify[] = ['success', 'Order notes updated successfully'];
        return back()->withNotify($notify);
    }

    public function cancel(Request $request){
  
        $request->validate([
            'order_id'=> 'required|integer'
        ]);
        /**
        * For knowing about status and order status 2 means Pending
        * @see \App\Models\Order go to status method 
        */
        $order = Order::where('status', 2)->findOrFail($request->order_id);
        $order->status = 3; //3 means Cancelled
        $order->save();

        $hostings = $order->hostings;
        $domains = $order->domains;

        //Trying to make Cancelled all items under the order, 5 means Cancelled
        Hosting::whereIn('id', $hostings->pluck('id'))->update(['status'=> 5]);
        Domain::whereIn('id', $domains->pluck('id'))->update(['status'=> 5]);

        //Trying to make Cancelled all items under the order
        $invoice = $order->invoice;
        foreach($invoice->items()->where('item_type', 1)->where('next_due_date', '!=', null)->groupBy('domain_id')->cursor() as $renewDomain){
            $domain = $renewDomain->domain;
            $domain->status = 5; //5 means Cancelled
            $domain->save();
        }   

        $notify[] = ['success', 'Order cancelled successfully'];
        return back()->withNotify($notify);
    }

    public function markPending(Request $request){

        $request->validate([
            'order_id'=> 'required|integer'
        ]);
        /**
        * For knowing about status and order status 2 means Pending
        * @see \App\Models\Order go to status method 
        */
        $order = Order::where('status', '!=', 2)->findOrFail($request->order_id);
        $order->status = 2; //2 means Pending
        $order->save();

        $hostings = $order->hostings;
        $domains = $order->domains;

        //Trying to make Pending all items under the order, 2 means Pending
        Hosting::whereIn('id', $hostings->pluck('id'))->update(['status'=> 2]);
        Domain::whereIn('id', $domains->pluck('id'))->update(['status'=> 2]);

        //Trying to make Pending all items under the order, 2 means Pending
        $invoice = $order->invoice;
        foreach($invoice->items()->where('item_type', 1)->where('next_due_date', '!=', null)->groupBy('domain_id')->cursor() as $renewDomain){
            $domain = $renewDomain->domain;
            $domain->status = 2; //2 means Pending
            $domain->save();
        }  

        $notify[] = ['success', 'Order set back to pending successfully'];
        return back()->withNotify($notify);
    }

    protected function orderData($scope = null){
 
        if($scope){
            $orders = Order::$scope()->with($this->with());
        }else{
            $orders = Order::with($this->with());
        }

        $orders = $orders->searchable(['user:username'])->dateFilter()->orderBy('id','desc')->paginate(getPaginate());
        return $orders;
    }

}


