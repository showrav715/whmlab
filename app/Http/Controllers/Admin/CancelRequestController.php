<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CancelRequest;
use Carbon\Carbon;

class CancelRequestController extends Controller{

    protected function with($with = []){
        $array = ['service.user', 'service.product.serviceCategory'];
        return array_merge($array, $with);
    }

    public function allRequests(Request $request){
        $pageTitle = 'All Cancellation Requests';
        $cancelRequests = $this->data();
        return view('admin.cancel_request.all', compact('pageTitle', 'cancelRequests'));
    }

    public function pending(){
        $pageTitle = 'Pending Cancellation Requests';
        $cancelRequests = $this->data('pending');
        return view('admin.cancel_request.all', compact('pageTitle', 'cancelRequests'));
    }

    public function completed(){
        $pageTitle = 'Completed Cancellation Requests';
        $cancelRequests = $this->data('completed');
        return view('admin.cancel_request.all', compact('pageTitle', 'cancelRequests'));
    }

    public function cancel(Request $request){

        $request->validate([
            'id'=>'required'
        ]);

        $findCancelRequest = CancelRequest::pending()->findOrFail($request->id);
        $findCancelRequest->status = 1; //Completed
        /**
        * For knowing about the status 
        * @see \App\Models\CancelRequest go to status method 
        */
        $findCancelRequest->save();

        $service = $findCancelRequest->service;
        $service->status = 5; //Cancelled 
        /**
        * For knowing about the status 
        * @see \App\Models\Hosting go to status method 
        */
        $service->save();
   
        $notify[] = ['success', 'Mark as cancellation successfully'];
        return back()->withNotify($notify);
    }

    public function delete(Request $request){

        $request->validate([
            'id'=>'required'
        ]);

        CancelRequest::findOrFail($request->id)->delete();
   
        $notify[] = ['success', 'Deleted cancellation request successfully'];
        return back()->withNotify($notify);
    }

    protected function data($scope = null){

        if($scope){
            $invoices = CancelRequest::$scope()->with($this->with());
        }else{
            $invoices = CancelRequest::with($this->with());
        }

        $invoices = $invoices->searchable(['user:username'])->dateFilter()->orderBy('id','desc')->paginate(getPaginate());
        return $invoices;
    }
 
}
