<?php

namespace App\Http\Controllers\User;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use App\Models\Hosting;
use App\Models\CancelRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller{
    
    public function list(){
        $pageTitle = 'Service List';
        $services = Hosting::whereBelongsTo(auth()->user())->orderBy('id', 'DESC')->with('product.serviceCategory')->paginate(getPaginate());
        return view('Template::user.service.list', compact('pageTitle', 'services'));
    }

    public function details($id){

        $pageTitle = 'Service Details';
        $service = Hosting::whereBelongsTo(auth()->user())->with('hostingConfigs.select', 'hostingConfigs.option', 'product.getConfigs.group.options')->findOrFail($id);

        $server = @$service->server;
        $serverGroup = @$server->group;
        
        $execute = HostingManager::init($serverGroup)->accountSummary($service);
        $accountSummary = @$execute['processed_data'];
        $cancelRequestTypes = CancelRequest::type();

        $product = $service->product;
        $status = $service->status;  
        $diskUsagePercent = @$accountSummary['disk_usage_percent'];
        $hasAccount = @$execute['raw_data'];

        return view('Template::user.service.details', compact('pageTitle', 'service', 'accountSummary', 'serverGroup', 'execute', 'cancelRequestTypes', 'diskUsagePercent', 'product', 'status', 'hasAccount'));
    }

    public function cancelRequest(Request $request){

        $request->validate([ 
            'id' => 'required|integer',
            'reason' => 'required',
            'cancellation_type' => 'required|in:'.CancelRequest::type(true),
        ]);

        $service = Hosting::whereBelongsTo(auth()->user())->whereDoesntHave('cancelRequest')->findOrFail($request->id);

        $cancelRequest = new CancelRequest();
        $cancelRequest->user_id = auth()->user()->id;
        $cancelRequest->hosting_id = $service->id;
        $cancelRequest->reason = $request->reason;
        $cancelRequest->type = $request->cancellation_type; 
        /**
        * For knowing about the type 
        * @see \App\Models\CancelRequest go to type method 
        */
        $cancelRequest->save();

        $notify[] = ['success', 'Your cancellation request submitted successfully'];
        return back()->withNotify($notify);
    }

}
