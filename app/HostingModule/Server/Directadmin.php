<?php

namespace App\HostingModule\Server;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Directadmin{

    public function create($hosting){
        
        try{
            $user = $hosting->user;
            $product = $hosting->product; 
            $server = $hosting->server;

            $response = Http::withBasicAuth($server->username, $server->password)
            ->asForm()
            ->post($server->hostname . '/CMD_API_ACCOUNT_USER?json=yes', [
                'username' => $hosting->username,
                'email' => $user->email,
                'passwd' => $hosting->password,
                'passwd2' => $hosting->password,
                'domain' => $hosting->domain,
                'package' => $product->package_name,
                'ip' => $this->getIP($server),
                'notify' => 'yes',
                'json' => 'yes',
                'add' => 'yes',
                'action' => 'create',
            ]);

            $response = json_decode($response, true);

            if(@$response['error']){
                $message = @$response['result'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $hosting->package_name = $product->package_name;
            $hosting->save(); 

            return [
                'success'=>true, 
                'message'=>null
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

            $response = Http::withBasicAuth($server->username, $server->password)
            ->asForm()
            ->post("$server->hostname/CMD_API_SELECT_USERS", [
                'location' => 'CMD_SELECT_USERS',
                'reason' => 'CMD_SELECT_USERS',
                'suspend' => 'suspend',
                'select0' => $hosting->username,
                'json' => 'yes',
                'action' => 'multiple',
            ]);

            $response = json_decode($response, true);
 
            if(@$response['error']){
                $message = @$response['error'];

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

            $response = Http::withBasicAuth($server->username, $server->password)
            ->asForm()
            ->post("$server->hostname/CMD_API_SELECT_USERS", [
                'location' => 'CMD_SELECT_USERS',
                'suspend' => 'no', // Changed from 'suspend' to 'no' to unsuspend
                'select0' => $hosting->username,
                'json' => 'yes',
                'action' => 'multiple',
            ]);

            $response = json_decode($response, true);
 
            if(@$response['error']){
                $message = @$response['error'];

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

            $response = Http::withBasicAuth($server->username, $server->password)
            ->get($server->hostname. '/CMD_API_SELECT_USERS', [
                'confirmed' => 'Confirm',        // required to confirm deletion
                'delete' => 'yes',               // tells DirectAdmin to delete
                'select0' => $hosting->username,        // the user to delete
                'json' => 'yes',
            ]);
            
            $response = json_decode($response, true);

            if(@$response['error']){
                $message = @$response['result'];

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

            $response = Http::withBasicAuth($server->username, $server->password)
            ->get($server->hostname . '/CMD_API_MODIFY_USER', [
                'action' => 'package',
                'user' => $hosting->username,
                'package' => $product->package_name,
                'json' => 'yes',
            ]);

            $response = json_decode($response, true);

            if(@$response['error']){
                $message = @$response['result'];

                $this->adminNotification($hosting, @$message);

                return [
                    'success'=> false, 
                    'message'=> @$message
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

            $response = Http::withBasicAuth($server->username, $server->password)
            ->asForm()
            ->post("$server->hostname/CMD_API_USER_PASSWD", [  // Changed endpoint
                'username' => $hosting->username,
                'passwd' => $hosting->password,
                'passwd2' => $hosting->password,
                'json' => 'yes',
            ]);

            $response = json_decode($response, true);

            if(@$response['error']){
                $message = @$response['result'];

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

            $response = Http::withBasicAuth($server->username, $server->password)
            ->get($server->hostname . '/CMD_API_SHOW_USER_CONFIG', [
                'user' => $hosting->username,
                'json' => 'yes',
            ]);

            $response = json_decode($response, true);

            return [
                'raw_data' => @$response['error'] ? null : $response,
                'processed_data' => $this->getProcessedAccountSummary($response),
            ];

        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
    }

    public function loginServer($server){
        
        try{
            $response = Http::withBasicAuth($server->username, $server->password)
            ->asForm()
            ->post($server->hostname . '/CMD_API_LOGIN_KEYS', [
                'action'     => 'create',
                'keyname'    => 'login_key_' . uniqid(),
                'type'       => 'one_time_url',
                'select'     => $server->username,
                'expire'     => time() + 300, // expires in 5 mins
                'json'       => 'yes',
            ]);

            $response = json_decode($response, true);

            if(@$response['error']){
                $message = @$response['error'];

                if($server->id){
                    $this->adminNotification(null, @$message, urlPath('admin.server.edit.page', $server->id));
                }

                return [
                    'success'=> false, 
                    'message'=> @$message
                ];
            }

            $redirectUrl = $response['result'];

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
            $impersonatedUser = $server->username . '|' . $hosting->username;

            $response = Http::withBasicAuth($impersonatedUser, $server->password)
            ->asForm()
            ->post($server->hostname . '/CMD_API_LOGIN_KEYS', [
                'action'     => 'create',
                'type'       => 'one_time_url',
                'keyname'    => 'login_key_' . uniqid(),
                'key'        => '', // Leave empty to auto-generate
                'expire'     => time() + 300, // Expires in 5 minutes
                'json'       => 'yes',
            ]);

            $response = json_decode($response, true);

            if(@$response['error']){
                $message = @$response['error'];

                $this->adminNotification($hosting, @$message);
                
                return [
                    'success'=>false, 
                    'message'=>@$message
                ];
            }

            $redirectUrl = @$response['result'];
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

    public function getIP($server){
  
        try{
            $response = Http::withBasicAuth($server->username, $server->password)
            ->asForm()
            ->get($server->hostname . '/CMD_API_SHOW_RESELLER_IPS', [
                'json' => 'yes',
            ]);

            $response = json_decode($response);
            return @$response[0] ?? null;
            
        }catch(\Exception  $error){
            Log::error($error->getMessage());
        }
    }
  
    public function getPackage($serverGroup){ 
        
        try{
            $packages = [];
            $servers = $serverGroup->servers;

            foreach($servers as $server){

                $response = Http::withBasicAuth($server->username, $server->password)->get($server->hostname . '/CMD_API_PACKAGES_USER', [
                    'json' => 'yes',
                ]);
    
                $response = json_decode($response);

                if(@$response['error']){
                    $message = @$response['error'];

                    if($server->id){
                        $this->adminNotification(null, @$message, urlPath('admin.server.edit.page', $server->id));
                    }

                    return [
                        'success'=> false, 
                        'message'=> @$message
                    ];
                }

                $packages[$server->id] = $response;
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
            "account",
            "additional_bandwidth",
            "aftp",
            "auto_security_txt",
            "bandwidth",
            "catchall",
            "cgi",
            "clamav",
            "comments",
            "creator",
            "cron",
            "date_created",
            "demo",
            "dnscontrol",
            "domain",
            "domainptr",
            "email",
            "email_limit",
            "ftp",
            "git",
            "inode",
            "ip",
            "ips",
            "is_reseller_skin",
            "jail",
            "language",
            "login_keys",
            "mysql",
            "name",
            "nemailf",
            "nemailml",
            "nemailr",
            "nemails",
            "notify_on_all_twostep_auth_failures",
            "ns1",
            "ns2",
            "nsubdomains",
            "package",
            "php",
            "quota",
            "redis",
            "reseller_can_reset_email_count",
            "skin",
            "spam",
            "ssh",
            "ssl",
            "suspend_at_limit",
            "suspended",
            "sysinfo",
            "twostep_auth",
            "user_email",
            "username",
            "usertype",
            "vdomains",
            "wordpress",
            "zoom"
        ];

        foreach($selectedKey as $key){
            if(isset($accountSummary[$key])){
                $summary[$key] = $accountSummary[$key];
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

 