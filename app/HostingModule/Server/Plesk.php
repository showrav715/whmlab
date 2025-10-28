<?php

namespace App\HostingModule\Server;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\HostingModule\Server\HostingManagerInterface;

class Plesk implements HostingManagerInterface{

    public function create($hosting){

        try{ 
            $user = $hosting->user;
            $product = $hosting->product; 
            $server = $hosting->server;

            $response = Http::withBasicAuth($server->username, $server->password)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
            ->post("$server->hostname/api/v2/clients", [
                "name" => $user->fullname,
                "login" => $hosting->username,
                "email" => $user->email,
                "password" => $hosting->password,
                "type" => "customer"
            ]);

            $response = json_decode($response, true);
            
            if(!@$response['guid']){
                $message = @$response['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->plesk_customer_id = @$response['id'];
            $hosting->package_name = $product->package_name;
            $hosting->save(); 

            $addDomainResponse = $this->addDomain($hosting);
   
            if(!$addDomainResponse['success']){ 
                return [
                    'success'=> false, 
                    'message'=> @$addDomainResponse['message']
                ];
            }

            return [
                'success'=>true, 
                'message'=> null
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }  

    protected function addDomain($hosting){

        try{
            $server = $hosting->server;
            $product = $hosting->product;

            $hostname = $server->hostname;
            $username = $server->username;
            $password = $server->password;

            $auth = base64_encode("$username:$password");
            $ipAddress = $this->getIP($server); 

            $xml = <<<XML
                <webspace>
                    <add>
                        <gen_setup>
                        <name>$hosting->domain</name>
                        <ip_address>$ipAddress</ip_address>
                        <htype>vrt_hst</htype>
                        <owner-id>$hosting->plesk_customer_id</owner-id>
                        </gen_setup>
                            <hosting>
                            <vrt_hst>
                            <property>
                                <name>ftp_login</name>
                                <value>$hosting->username</value>
                            </property>
                            <property>
                                <name>ftp_password</name>
                                <value>$hosting->password</value>
                            </property>
                            <ip_address>$ipAddress</ip_address>
                            </vrt_hst>
                        </hosting>
                        <plan-name>$product->package_name</plan-name>
                    </add>
                </webspace>
            XML;

            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
                'HTTP_AUTH_LOGIN' => $username,
                'HTTP_AUTH_PASSWD' => $password,
                'Authorization' => "Basic ".$auth,
            ])->withBody($xml, 'text/xml')->post("$hostname/enterprise/control/agent.php");

            $xml = simplexml_load_string($response->body());
            $json = json_decode(json_encode($xml), true);
                
            if((@$json['webspace']['add']['result']['status'] == 'error') || (@$json['system']['status'] == 'error')){
                $message = @$json['webspace']['add']['result']['errtext'] ?? @$json['system']['errtext'];
                
                $this->adminNotification($hosting, @$message);

                return [
                    'success'=> false, 
                    'message'=>@$message
                ];
            }

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

    public function suspend($data){
        
        try{
            $hosting = $data['hosting'];
            $server = $hosting->server;
            $request = $data['request'];

            $response = Http::withBasicAuth($server->username, $server->password)->put("$server->hostname/api/v2/clients/$hosting->plesk_customer_id/suspend");
            $response = json_decode($response, true);

            if(@$response['status'] != 'success'){
                $message = @$response['message'];

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
            $response = Http::withBasicAuth($server->username, $server->password)->put("$server->hostname/api/v2/clients/$hosting->plesk_customer_id/activate");
            $response = json_decode($response, true);

            if(@$response['status'] != 'success'){
                $message = @$response['message'];

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
            $response = Http::withBasicAuth($server->username, $server->password)->delete("$server->hostname/api/v2/clients/$hosting->plesk_customer_id");
            $response = json_decode($response, true);

            if(!@$response['guid']){
                $message = @$response['message'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->termination_date = now();
            $hosting->plesk_customer_id = 0;
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

            $hostname = $server->hostname;
            $username = $server->username;
            $password = $server->password;

            $auth = base64_encode("$username:$password");
            $GUID = $this->getPlanGuid($hosting);

            $xml = <<<XML
                <packet version="1.6.3.0">
                <webspace>
                    <switch-subscription>
                    <filter>
                        <name>$hosting->domain</name> <!-- or use <id>12345</id> -->
                    </filter>
                    <plan-guid>$GUID</plan-guid>
                    </switch-subscription>
                </webspace>
                </packet>
            XML;

            $response = Http::withHeaders([
                    'Content-Type' => 'text/xml',
                    'HTTP_AUTH_LOGIN' => $username,
                    'HTTP_AUTH_PASSWD' => $password,
                    'Authorization' => "Basic $auth",
                ])
                ->withBody($xml, 'text/xml')
            ->post("$hostname/enterprise/control/agent.php");

            $xml = simplexml_load_string($response->body());
            $json = json_decode(json_encode($xml), true);

            if(@$json['webspace']['add']['result']['status'] == 'error'){
                $message = @$json['webspace']['add']['result']['errtext'];
                
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

    protected function getPlanGuid($hosting){
        
        try{
            $server = $hosting->server;
            $product = $hosting->product;
            $packageName = $product->package_name;

            $hostname = $server->hostname;
            $username = $server->username;
            $password = $server->password;

            $auth = base64_encode("$username:$password");

            $xml = <<<XML
                <packet version="1.6.3.0">
                <service-plan>
                    <get>
                    <filter>
                        <name>$packageName</name>
                    </filter>
                    </get>
                </service-plan>
                </packet>
            XML;

            $response = Http::withHeaders([
                    'Content-Type' => 'text/xml',
                    'HTTP_AUTH_LOGIN' => $username,
                    'HTTP_AUTH_PASSWD' => $password,
                    'Authorization' => "Basic $auth",
                ])
                ->withBody($xml, 'text/xml')
            ->post("$hostname/enterprise/control/agent.php");

            $xml = simplexml_load_string($response->body());
            $json = json_decode(json_encode($xml), true);

            return @$json['service-plan']['get']['result']['guid'] ?? null;

        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
    }

    public function changePassword($hosting){

        try{
            $server = $hosting->server;
            $response = Http::withBasicAuth($server->username, $server->password)
            ->put("$server->hostname/api/v2/clients/$hosting->plesk_customer_id", [
                'password' => $hosting->password,
            ]);
            
            $response = json_decode($response, true);

            if(!@$response['guid']){
                $message = @$response['message'];

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
            $response = Http::withBasicAuth($server->username, $server->password)->get("$server->hostname/api/v2/clients/$hosting->plesk_customer_id/statistics");
            $response = json_decode($response);

            return [
                'raw_data' => @$response->message ? null : $response,
                'processed_data' => $this->getProcessedAccountSummary($response),
            ];

        }catch(\Exception  $error){ 
            Log::error($error->getMessage());
        }
    }

    public function loginServer($server){

        try{
            $hostname = $server->hostname;
            $username = $server->username;
            $password = $server->password;
            $auth = base64_encode("$username:$password");

            $xmlRequest = <<<XML
                <packet version="1.6.3.0">
                <server>
                    <create_session>
                    <login>$username</login>
                    </create_session>
                </server>
                </packet>
            XML;

            $response = Http::withHeaders([
                    'Content-Type' => 'text/xml',
                    'HTTP_AUTH_LOGIN' => $username,
                    'HTTP_AUTH_PASSWD' => $password,
                    'Authorization' => "Basic $auth",
                ])
                ->withBody($xmlRequest, 'text/xml')
            ->post("$hostname/enterprise/control/agent.php");

            $xml = simplexml_load_string($response->body());
            $json = json_decode(json_encode($xml), true);

            if(@$json['system']['status'] == 'error'){
                $message = @$json['system']['errtext'];
                
                if($server->id){
                    $this->adminNotification(null, @$message, urlPath('admin.server.edit.page', $server->id));
                }
                
                return [
                    'success'=> false, 
                    'message'=> @$message
                ];
            }
            
            $sessionId = @$json['server']['create_session']['result']['id'];  
            $redirectUrl = "$hostname/enterprise/rsession_init.php?PLESKSESSID=$sessionId";

            return [
                'success'=> true, 
                'url'=> $redirectUrl
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

            $hostname = $server->hostname;
            $username = $server->username;
            $password = $server->password;
            $auth = base64_encode("$username:$password");

            $xmlRequest = <<<XML
                <packet version="1.6.3.0">
                <server>
                    <create_session>
                    <login>$hosting->username</login>
                    </create_session>
                </server>
                </packet>
            XML;

            $response = Http::withHeaders([
                    'Content-Type' => 'text/xml',
                    'HTTP_AUTH_LOGIN' => $username,
                    'HTTP_AUTH_PASSWD' => $password,
                    'Authorization' => "Basic $auth",
                ])
                ->withBody($xmlRequest, 'text/xml')
            ->post("$hostname/enterprise/control/agent.php");

            $xml = simplexml_load_string($response->body());
            $json = json_decode(json_encode($xml), true);

            if(@$json['server']['create_session']['result']['status'] == 'error'){
                $message = @$json['server']['create_session']['result']['errtext'];
                
                $this->adminNotification($hosting, @$message);
                
                return [
                    'success'=> false, 
                    'message'=> @$message
                ];
            }

            $sessionId = @$json['server']['create_session']['result']['id'];  
            $redirectUrl = "$hostname/enterprise/rsession_init.php?PLESKSESSID=$sessionId";

            return [
                'success'=> true, 
                'url'=> $redirectUrl
            ];

        }catch(\Exception  $error){
            return [
                'success'=>false, 
                'message'=>$error->getMessage()
            ];
        }
    }
  
    public function getIP($server){

        try{
            $hostname = $server->hostname;
            $username = $server->username;
            $password = $server->password;
            $auth = base64_encode("$username:$password");

            $xmlRequest = <<<XML
                <packet>
                    <ip>
                        <get/>
                    </ip>
                </packet>
            XML;

            $response = Http::withHeaders([
                    'Content-Type' => 'text/xml',
                    'HTTP_AUTH_LOGIN' => $username,
                    'HTTP_AUTH_PASSWD' => $password,
                    'Authorization' => "Basic $auth",
                ])
                ->withBody($xmlRequest, 'text/xml')
            ->post("$hostname/enterprise/control/agent.php");

            $xml = simplexml_load_string($response->body());
            $json = json_decode(json_encode($xml), true);

            return @$json['ip']['get']['result']['addresses']['ip_info']['ip_address'] ?? null;
            
        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
    }

    public function getPackage($serverGroup){ 

        try{
            $packages = [];
            $servers = $serverGroup->servers;

            foreach($servers as $server){

                $auth = base64_encode("$server->username:$server->password");
                
                $xmlRequest = <<<XML
                    <packet>
                    <service-plan>
                        <get>
                        <filter/>
                        </get>
                    </service-plan>
                    </packet>
                XML;

                $response = Http::withHeaders([
                        'Content-Type' => 'text/xml',
                        'HTTP_AUTH_LOGIN' => $server->username,
                        'HTTP_AUTH_PASSWD' => $server->password,
                        'Authorization' => "Basic $auth",
                    ])
                    ->withBody($xmlRequest, 'text/xml') 
                ->post("$server->hostname/enterprise/control/agent.php");

                $xml = simplexml_load_string($response->body());
                $json = json_decode(json_encode($xml), true);

                if(@$json['system']['status'] == 'error'){
                    $message = @$json['system']['errtext'];
                    
                    return [
                        'success'=> false, 
                        'message'=> @$message
                    ];
                }

                $packages[$server->id] = array_column(@$json['service-plan']['get']['result'], 'name');
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
            "active_domains",
            "subdomains",
            "disk_space",
            "email_postboxes",
            "email_redirects",
            "email_response_messages",
            "mailing_lists",
            "databases",
            "traffic",
            "traffic_prevday"
        ];

        foreach($selectedKey as $key){
            if(isset($accountSummary->$key)){
                $summary[$key] = $accountSummary->$key;
            }else{
                $summary[$key] = null;
            }
        }

        return $summary;
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

 