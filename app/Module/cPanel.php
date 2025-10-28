<?php

namespace App\Module;

use App\Models\AdminNotification;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class cPanel{

    public $request;
    public $username; 
    public $password;
    public $hostname;
    public $apiToken;
    public $serverGroup;
    public $securityToken;

    public function create($hosting){

        $user = $hosting->user;
        $product = $hosting->product; 
        $server = $hosting->server;
        
        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/createacct?api.version=1&username='.$hosting->username.'&domain='.$hosting->domain.'&contactemail='.$user->email.'&password='.$hosting->password.'&pkgname='.$product->package_name);
    
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
     
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $hosting->ns1 = $response->data->nameserver;
            $hosting->ns2 = $response->data->nameserver2;
            $hosting->ns3 = $response->data->nameserver3;
            $hosting->ns4 = $response->data->nameserver4;
            $hosting->package_name = $product->package_name;
            $hosting->ip = $response->data->ip;
            $hosting->save(); 

            return ['success'=>true, 'message'=>$response];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }

    }  

    public function suspend($hosting, $request){
      
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/suspendacct?api.version=1&user='.$hosting->username.'&reason='.$request->suspend_reason);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $hosting->suspend_reason = $request->suspend_reason;
            $hosting->suspend_date = now();
            $hosting->save();

            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function unSuspend($hosting){

        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/unsuspendacct?api.version=1&user='.$hosting->username);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);
                return ['success'=>false, 'message'=>@$message];
            }
            
            $hosting->suspend_reason = null;
            $hosting->suspend_date= null;
            $hosting->save();

            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function terminate($hosting){
        $server = $hosting->server;

        try{
     
            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/removeacct?api.version=1&username='.$hosting->username);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
   
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $hosting->termination_date = now();
            $hosting->save();

            return ['success'=>true, 'message'=>explode('\n', $response->metadata->reason)[0]];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function changePackage($hosting){
        $server = $hosting->server;
        $product = $hosting->product;
      
        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/changepackage?api.version=1&user='.$hosting->username.'&pkg='.$product->package_name);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $hosting->package_name = $product->package_name;
            $hosting->save();

            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function changePassword($hosting){
        $server = $hosting->server;

        try{

            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/passwd?api.version=1&user='.$hosting->username.'&password='.$hosting->password);
 
            $response = json_decode($response);
            $responseStatus = $this->whmApiResponse($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            return ['success'=>true, 'message'=>explode('\n', $response->metadata->reason)[0]];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function accountSummary($server){
      
        if(!$server){
            return ;
        }

        try{
            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/accountsummary?api.version=1&user='.$this->username);
            
            $response = json_decode(@$response)->data->acct[0];
            return $response;

        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }

    }

    public function whm($login = false){
     
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode($this->username.':'.$this->password),
            ])->get($this->hostname.'/json-api/create_user_session?api.version=1&user='.$this->username.'&service=whostmgrd');
    
            $response = json_decode($response);
           
            if(@$response->cpanelresult->error){
                $message = @$response->cpanelresult->data->reason;

                $server = Server::where('username', $this->username)->where('password', $this->password)->first('id');
                $this->adminNotification(null, @$message, urlPath('admin.server.edit.page', $server->id));
                return ['success'=>false, 'message'=>@$message];
            }

            if($login){
                $redirectUrl = $response->data->url;
                return ['success'=>true, 'message'=>'OK', 'url'=>$redirectUrl];
            }

            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }

    }

    //Trying to get IP address from WHM API
    public function getIP(){

        try{
            $response = Http::withHeaders([
                'Authorization' => 'WHM '.$this->username.':'.$this->apiToken,
            ])->get($this->hostname.'/cpsess'.$this->securityToken.'/json-api/accountsummary?api.version=1&user='.$this->username);

            $response = json_decode(@$response);
            return @$response->data->acct[0]->ip ?? null;
            
        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
        
    }
  
    public function getWhmPackage(){ 
        
        try{
         
            $packages = [];
            $serverGroup = $this->serverGroup;
            $servers = $serverGroup->servers;
            
            foreach($servers as $server){
                
                $response = Http::withHeaders([
                    'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
                ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/listpkgs?api.version=1');
    
                $response = json_decode($response);
            
                if($response->metadata->result == 0){

                    if(str_contains($response->metadata->reason, '. at') !== false){
                        $message = explode('. at', $response->metadata->reason)[0];
                    }else{
                        $message = $response->metadata->reason;
                    }

                    return response()->json(['success'=>false, 'message'=>$message]);
                } 

                $packages[$server->id] = array_column(@$response->data->pkg, 'name');
            }

            return response()->json(['success'=>true, 'data'=>$packages]);

        }catch(\Exception  $error){
            return response()->json(['success'=>false, 'message'=>$error->getMessage()]);
        }
        
    }

    public function loginCpanel($hosting, $server){
    
        try{
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode($server->username.':'.$server->password),
            ])->get($server->hostname.'/json-api/create_user_session?api.version=1&user='.$hosting->username.'&service=cpaneld');
    
            $response = json_decode($response);
           
            if(@$response->cpanelresult->error || !@$response->metadata->result){
                $message = $response->cpanelresult->data->reason ?? @$response->metadata->reason;

                $this->adminNotification($hosting, @$message);
                return ['success'=>false, 'message'=>@$message];
            }
          
            $redirectUrl = $response->data->url;
            return ['success'=>true, 'message'=>'OK', 'url'=>$redirectUrl];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }

    }

    protected function whmApiResponse($response){
        $success = true;
        $message = null;

        if($response->metadata->result == 0){

            $success = false;

            if(str_contains($response->metadata->reason, '. at') !== false){
                $message = explode('. at', $response->metadata->reason)[0];
            }else{
                $message = $response->metadata->reason;
            }
        }

        return ['success'=>$success, 'message'=>$message];
    }

    protected function adminNotification($data = null, $message, $url = null){
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = @$data->user_id ?? 0;
        $adminNotification->title = gettype($message) == 'array' ? implode('. ', $message) : $message;
        $adminNotification->api_response = 1;
        $adminNotification->click_url = $url ? $url : urlPath('admin.order.hosting.details', $data->id);
        $adminNotification->save();
    }

}

 