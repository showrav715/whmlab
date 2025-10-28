<?php

namespace App\Http\Controllers\Admin;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hosting;
use App\Lib\SendServiceEmail;

class HostingModuleController extends Controller{

    public function moduleCommand(Request $request){

        $request->validate([
            'hosting_id'=> 'required', 
            'module_type'=> 'required|numeric|between:1,6',
            'suspend_reason'=> 'required_if:module_type,==,2'
        ]);

        $hosting = Hosting::findOrFail($request->hosting_id);
        $server = @$hosting->server;
        $serverGroup = @$server->group;

        if(!$hosting->server_id){
            $notify[] = ['error', 'Select server before running the module command'];
            return back()->withNotify($notify);
        }

        if($request->module_type == 1){ 
            return $this->create($serverGroup, $hosting);
        }
        elseif($request->module_type == 2){
            return $this->suspend($serverGroup, $hosting, $request);
        }
        elseif($request->module_type == 3){
            return $this->unSuspend($serverGroup, $hosting, $request);
        }
        elseif($request->module_type == 4){
            return $this->terminate($serverGroup, $hosting);
        }
        elseif($request->module_type == 5){
            return $this->changePackage($serverGroup, $hosting);
        }
        elseif($request->module_type == 6){
            return $this->changePassword($serverGroup, $hosting);
        }
    }

    protected function create($serverGroup, $hosting){
        
        $execute = HostingManager::init($serverGroup)->create($hosting);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
        /**
        * For knowing about status 
        * @see \App\Models\Hosting go to status method 
        */
        $hosting->status = 1; //1 means Active
        $hosting->save(); 

        $notify[] = ['success', 'Create module command run successfully'];
        return back()->withNotify($notify)->with('response', $execute['message']);
    } 

    protected function suspend($serverGroup, $hosting, $request){
      
        $execute = HostingManager::init($serverGroup)->suspend([
            'hosting' => $hosting,
            'request' => $request,
        ]);
        
        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        if($request->suspend_email){
            SendServiceEmail::serviceSuspend($hosting, $request);
        }
        /**
        * For knowing about status 
        * @see \App\Models\Hosting go to status method 
        */
        $hosting->status = 3; //3 means Suspended
        $hosting->save(); 

        $notify[] = ['success', 'Suspension of '.$hosting->username.' user'];
        return back()->withNotify($notify);
    }

    protected function unSuspend($serverGroup, $hosting, $request){

        $execute = HostingManager::init($serverGroup)->unSuspend($hosting);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        if($request->unSuspend_email){
            SendServiceEmail::serviceUnsuspend($hosting, $request);
        }
        /**
        * For knowing about status 
        * @see \App\Models\Hosting go to status method 
        */
        $hosting->status = 1; //1 means Active
        $hosting->save(); 

        $notify[] = ['success', 'Unsuspension account of '.$hosting->username.' user'];
        return back()->withNotify($notify);
    }

    protected function terminate($serverGroup, $hosting){
 
        $execute = HostingManager::init($serverGroup)->terminate($hosting);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
        /**
        * For knowing about status 
        * @see \App\Models\Hosting go to status method 
        */
        $hosting->status = 4; //4 means Terminated
        $hosting->save(); 

        $notify[] = ['success', $execute['message']];
        return back()->withNotify($notify);
    }

    protected function changePackage($serverGroup, $hosting){
        
        $execute = HostingManager::init($serverGroup)->changePackage($hosting);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Changed package for '.$hosting->username.' user'];
        return back()->withNotify($notify);
    }

    protected function changePassword($serverGroup, $hosting){

        $execute = HostingManager::init($serverGroup)->changePassword($hosting);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', $execute['message']];
        return back()->withNotify($notify);
    }

    public function loginHosting(Request $request){
   
        $request->validate([
            'hosting_id'=> 'required'
        ]);

        $hosting = Hosting::findOrFail($request->hosting_id);
        $server = $hosting->server;
        $serverGroup = $server->group;

        if(!$server){
            $notify[] = ['error', 'There is no selected server to auto-login'];
            return back()->withNotify($notify); 
        }

        $execute = HostingManager::init($serverGroup)->loginAccount($hosting);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        return back()->with('hostingLoginUrl', $execute['url']);
    }

}
