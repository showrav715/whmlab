<?php

namespace App\HostingModule\Server;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\HostingModule\Server\HostingManagerInterface;

class Cpanel implements HostingManagerInterface{

    public function create($hosting){
        
        try{
            $user = $hosting->user;
            $product = $hosting->product; 
            $server = $hosting->server;
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/createacct?api.version=1&username='.$hosting->username.'&domain='.$hosting->domain.'&contactemail='.$user->email.'&password='.$hosting->password.'&pkgname='.$product->package_name);
    
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
     
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->ns1 = $response->data->nameserver;
            $hosting->ns2 = $response->data->nameserver2;
            $hosting->ns3 = $response->data->nameserver3;
            $hosting->ns4 = $response->data->nameserver4;
            $hosting->package_name = $product->package_name;
            $hosting->ip = $response->data->ip;
            $hosting->save(); 

            return [
                'success'=>true, 
                'message'=>$response
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }  

    public function suspend($data){
        
        try{
            $hosting = $data['hosting'];
            $server = $hosting->server;
            $request = $data['request'];
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/suspendacct?api.version=1&user='.$hosting->username.'&reason='.$request->suspend_reason);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->suspend_reason = $request->suspend_reason;
            $hosting->suspend_date = now();
            $hosting->save();

            return [
                'success'=>true, 
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function unSuspend($hosting){

        try{
            $server = $hosting->server;
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/unsuspendacct?api.version=1&user='.$hosting->username);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }
            
            $hosting->suspend_reason = null;
            $hosting->suspend_date= null;
            $hosting->save();

            return [
                'success'=>true
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function terminate($hosting){
        
        try{
            $server = $hosting->server;
            $token = 'WHM '.$server->username.':'.$server->api_token;
     
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/removeacct?api.version=1&username='.$hosting->username);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
   
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false,
                    'message'=>@$message
                ];
            }

            $hosting->termination_date = now();
            $hosting->save();

            return [
                'success'=> true, 
                'message'=> 'Account terminated successfully'
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function changePackage($hosting){
        
        try{
            $server = $hosting->server;
            $product = $hosting->product;
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/changepackage?api.version=1&user='.$hosting->username.'&pkg='.$product->package_name);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->package_name = $product->package_name;
            $hosting->save();

            return [
                'success'=>true
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function changePassword($hosting){

        try{
            $server = $hosting->server;
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/passwd?api.version=1&user='.$hosting->username.'&password='.$hosting->password);
 
            $response = json_decode($response);
            $responseStatus = $this->whmResponseStatus($response);
 
            if(!@$responseStatus['success']){
                $message = @$responseStatus['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            return [
                'success'=> true, 
                'message'=> 'Password changed successfully'
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function accountSummary($hosting){

        try{
            $server = $hosting->server;
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/accountsummary?api.version=1&user='.$hosting->username);
            
            $response = json_decode($response);
            $data = @$response->data->acct[0];
   
            return [
                'raw_data' => $data,
                'processed_data' => $this->getProcessedAccountSummary(@$response->data->acct[0]),
            ];

        }catch(\Exception  $error){ 
            Log::error($error->getMessage());
        }
    }

    public function loginServer($server){

        try{
            $token = 'Basic '.base64_encode($server->username.':'.$server->password);

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/json-api/create_user_session?api.version=1&user='.$server->username.'&service=whostmgrd');
    
            $response = json_decode($response);
           
            if(@$response->cpanelresult->error){
                $message = @$response->cpanelresult->data->reason;

                if($server->id){
                    $this->adminNotification(null, @$message, urlPath('admin.server.edit.page', $server->id));
                }

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $redirectUrl = $response->data->url;
            return [
                'success'=>true, 
                'url'=>$redirectUrl
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    public function loginAccount($hosting){

        try{
            $server = $hosting->server;
            $token = 'Basic '.base64_encode($server->username.':'.$server->password);

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/json-api/create_user_session?api.version=1&user='.$hosting->username.'&service=cpaneld');
    
            $response = json_decode($response);
           
            if(@$response->cpanelresult->error || !@$response->metadata->result){
                $message = $response->cpanelresult->data->reason ?? @$response->metadata->reason;

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }
          
            $redirectUrl = $response->data->url;
            return [
                'success'=>true, 
                'url'=>$redirectUrl
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    //Trying to get IP address from WHM API
    public function getIP($server){

        try{
            $token = 'WHM '.$server->username.':'.$server->api_token;

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/accountsummary?api.version=1&user='.$server->username);

            $response = json_decode(@$response);
            return @$response->data->acct[0]->ip ?? null;
            
        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
    }
  
    public function getPackage($serverGroup){ 
        
        try{
            $packages = [];
            $servers = $serverGroup->servers;
            
            foreach($servers as $server){
                
                $response = Http::withHeaders([
                    'Authorization' => 'WHM '.$server->username.':'.$server->api_token,
                ])->get($server->hostname.'/cpsess'.$server->security_token.'/json-api/listpkgs?api.version=1');
    
                $response = json_decode($response);
            
                if(@$response->metadata->result == 0){

                    if(str_contains(@$response->metadata->reason, '. at') !== false){
                        $message = explode('. at', @$response->metadata->reason)[0];
                    }else{
                        $message = @$response->metadata->reason;
                    }

                    return [
                        'success'=>false, 
                        'message'=>$message
                    ];
                } 

                $packages[$server->id] = array_column(@$response->data->pkg, 'name');
            }

            return [
                'success'=>true, 
                'data'=>$packages
            ]; 

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }

    protected function getProcessedAccountSummary($accountSummary){

        $summary = [];
        $selectedKey = [
            "outgoing_mail_suspended",
            "backup",
            "user",
            "plan",
            "maxpop",
            "legacy_backup",
            "max_defer_fail_percentage",
            "maxftp",
            "max_emailacct_quota",
            "uid",
            "maxsql",
            "theme",
            "suspendreason",
            "diskused",
            "domain",
            "ip",
            "maxparked",
            "maxaddons",
            "temporary",
            "min_defer_fail_to_trigger_protection",
            "is_locked",
            "startdate",
            "unix_startdate",
            "maxlst",
            "partition",
            "email",
            "outgoing_mail_hold",
            "disklimit",
            "maxsub",
            "suspended",
            "inodeslimit",
            "shell",
            "mailbox_format",
            "inodesused",
            "max_email_per_hour",
            "owner",
            "suspendtime"
        ];

        foreach($selectedKey as $key){
            if(isset($accountSummary->$key)){
                $summary[$key] = $accountSummary->$key;
            }else{
                $summary[$key] = null;
            }
        }

        $used = (int) @$accountSummary->diskused;
        $limit = (int) @$accountSummary->disklimit;

        if ($limit == 'unlimited' || $used == 0) {
            $used = 0;
            $limit = 1;
        }

        $diskUsagePercent = ($used / $limit) * 100;
        $summary['disk_usage_percent'] = $accountSummary ? round($diskUsagePercent, 2) . '%' : null;

        return $summary;
    }

    protected function whmResponseStatus($response){

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

        return [
            'success'=>$success, 
            'message'=>$message
        ];
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

 