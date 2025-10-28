<?php

namespace App\DomainRegisters;

use App\Models\Admin;
use App\Models\DomainSetup;
use App\Models\AdminNotification;
use App\Models\DomainRegister;
use Illuminate\Support\Facades\Http;

class Namecheap{

    public $url;
    public $domain; 
    public $request;
    public $username;
    public $requestIP; 
    public $namecheapAcc;
    public $register;
    public $singleSearch;

    public function __construct($domain){		
		$this->domain = $domain;
        $this->requestIP  = getRealIP(); 
        
        $this->register = DomainRegister::where('alias', 'Namecheap')->firstOrFail();
        $this->url = $this->register->test_mode ? 'https://api.sandbox.namecheap.com/xml.response' : 'https://api.namecheap.com/xml.response';
      
        $this->namecheapAcc = $this->register->params;
        $this->username = $this->register->test_mode ? $this->namecheapAcc->sandbox_username->value : $this->namecheapAcc->username->value;
	}

    protected function makeNameservers($request, $domain, $noChange = false){
        
        $nameservers = null;
        $server = @$domain->hosting->server;

        //When users try to change their nameservers
        if($request){
            $nameservers = $request->ns1.','.$request->ns2;
            
            if($request->ns3){
                $nameservers .= ','.$request->ns3;
            }
    
            if($request->ns4){
                $nameservers .= ','.$request->ns4;
            }

            return $nameservers;
        } 
   
        //When admin try to change their nameservers
        if($noChange){ 
            $nameservers = $domain->ns1.','.$domain->ns2;

            if($domain->ns3){
                $nameservers .= ','.$domain->ns3;
            }
    
            if($domain->ns4){
                $nameservers .= ','.$domain->ns4;
            }

            return $nameservers;
        }
        
        //If a server available for the domain
        if(@$server){  
            $nameservers = $server->ns1.','.$server->ns2;
   
            if($server->ns3){
                $nameservers .= ','.$server->ns3;
            }
    
            if($server->ns4){
                $nameservers .= ','.$server->ns4;
            }
           
            return $nameservers;
        }
     
        //If nameservers exist on the domain  
        if($domain->ns1 && $domain->ns2){
            $nameservers = $domain->ns1.','.$domain->ns2;

            if($domain->ns3){
                $nameservers .= ','.$domain->ns3;
            }
    
            if($domain->ns4){
                $nameservers .= ','.$domain->ns4;
            }

            return $nameservers;
        }
       
        //When there are no nameservers
        $nameservers = $this->register->ns1.','.$this->register->ns2;

        if($this->register->ns3){
            $nameservers .= ','.$this->register->ns3;
        }

        if($this->register->ns4){
            $nameservers .= ','.$this->register->ns4;
        }
        
        return $nameservers;
    }

    public function register(){
        
        $domain = $this->domain;
        $request = $this->request;
   
        $nameservers = $this->makeNameservers($request, $domain);
       
        $array = explode(',', $nameservers);
        $ns1 = @$array[0];
        $ns2 = @$array[1];
        $ns3 = @$array[2];
        $ns4 = @$array[3];

        $admin = Admin::first();
        $user = $domain->user;

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $dialCode = @$countryData[@$user->country_code]->dial_code;
        $countCode = strlen($dialCode);
 
        $withoutCode = substr($user->mobile, $countCode);
        $phone = '+'.@$dialCode.'.'.$withoutCode;
 
        try{   

            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.create',
                'ClientIp'=>$this->requestIP,

                'DomainName'=>$domain->domain,
                'Years'=>$domain->reg_period,
                'Nameservers'=>$nameservers,

                'AuxBillingFirstName'=>$user->firstname,
                'AuxBillingLastName'=>$user->lastname,
                'AuxBillingAddress1'=>@$user->address ?? 'N/A',
                'AuxBillingStateProvince'=>@$user->state ?? 'N/A',
                'AuxBillingPostalCode'=>@$user->zip ?? 'N/A',
                'AuxBillingCountry'=>@$user->country_code ?? 'N/A',
                'AuxBillingPhone'=>$phone,
                'AuxBillingEmailAddress'=>$user->email,
                'AuxBillingCity'=>@$user->city ?? 'N/A',

                'TechFirstName'=>$user->firstname,
                'TechLastName'=>$user->lastname,
                'TechAddress1'=>@$user->address ?? 'N/A',
                'TechStateProvince'=>@$user->state ?? 'N/A',
                'TechPostalCode'=>@$user->zip ?? 'N/A',
                'TechCountry'=>@$user->country_code ?? 'N/A',
                'TechPhone'=>$phone,
                'TechEmailAddress'=>$user->email,
                'TechCity'=>@$user->city ?? 'N/A',

                'AdminFirstName'=>explode(' ', @$admin->name)[0] ?? 'Super',
                'AdminLastName'=>explode(' ', @$admin->name)[1] ?? 'Admin',
                'AdminAddress1'=>@$admin->address->address ?? 'N/A',
                'AdminStateProvince'=>@$admin->address->state ?? 'N/A',
                'AdminPostalCode'=>@$admin->address->zip ?? 'N/A',
                'AdminCountry'=>@$admin->address->country ?? 'N/A',
                'AdminPhone'=>$admin->mobile,
                'AdminEmailAddress'=>$admin->email,
                'AdminCity'=>@$admin->address->city ?? 'N/A', 

                'RegistrantFirstName'=>$user->firstname,
                'RegistrantLastName'=>$user->lastname,
                'RegistrantAddress1'=>@$user->address ?? 'N/A',
                'RegistrantStateProvince'=>@$user->state ?? 'N/A',
                'RegistrantPostalCode'=>@$user->zip ?? 'N/A',
                'RegistrantCountry'=>@$user->country_code ?? 'N/A',
                'RegistrantPhone'=>$phone,
                'RegistrantEmailAddress'=>$user->email,
                'RegistrantCity'=>@$user->country_code ?? 'N/A',
    
                'Whoisguard'=>'yes',
                'AddFreeWhoisguard'=>'yes',

                'WGEnabled'=>$domain->id_protection ? 'yes' : 'no',
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                $message = @$response['Errors']['Error'];

                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $getInfo = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'ClientIp'=>$this->requestIP,
                'Command'=>'namecheap.domains.getinfo',
                'DomainName'=>$domain->domain,
            ]);

            $domainId = xmlToArray(@$getInfo)['CommandResponse']['DomainGetInfoResult']['Whoisguard']['ID']; 

            $domain->ns1 = @$ns1; 
            $domain->ns2 = @$ns2; 
            $domain->ns3 = @$ns3; 
            $domain->ns4 = @$ns4; 
            $domain->whois_guard = @$domainId; 
            $domain->status = 1; 
            $domain->save(); 

            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
	}

    public function renew(){

        $domain = $this->domain;
        $request = $this->request;
      
        try{
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.renew',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,
                'Years'=>$request ? $request->renew_year : $domain->reg_period,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                $message = @$response['Errors']['Error'];
                
                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }
    
            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function getContact(){

        $domain = $this->domain;

        try{
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.getContacts',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,
            ]);
     
            $response = xmlToArray(@$response);
            if(@$response['Errors']){
                $message = @$response['Errors']['Error'];
                
                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }
            
        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }

        return ['success'=>true, 'response'=>$response];
    }

    public function setContact(){

        $domain = $this->domain;
        $request = $this->request;

        try{   
 
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.setContacts',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,

                'AuxBillingFirstName'=>$request->AuxBillingFirstName,
                'AuxBillingLastName'=>$request->AuxBillingLastName,
                'AuxBillingAddress1'=>@$request->AuxBillingAddress1,
                'AuxBillingStateProvince'=>@$request->AuxBillingStateProvince,
                'AuxBillingPostalCode'=>@$request->AuxBillingPostalCode,
                'AuxBillingCountry'=>$request->AuxBillingCountry,
                'AuxBillingPhone'=>$request->AuxBillingPhone,
                'AuxBillingEmailAddress'=>$request->AuxBillingEmailAddress,
                'AuxBillingCity'=>@$request->AuxBillingCity,

                'TechFirstName'=>$request->TechFirstName,
                'TechLastName'=>$request->TechLastName,
                'TechAddress1'=>@$request->TechAddress1,
                'TechStateProvince'=>@$request->TechStateProvince,
                'TechPostalCode'=>@$request->TechPostalCode,
                'TechCountry'=>$request->TechCountry,
                'TechPhone'=>$request->TechPhone,
                'TechEmailAddress'=>$request->TechEmailAddress,
                'TechCity'=>@$request->TechCity,

                'AdminFirstName'=>$request->AdminFirstName,
                'AdminLastName'=>$request->AdminLastName,
                'AdminAddress1'=>@$request->AdminAddress1,
                'AdminStateProvince'=>@$request->AdminStateProvince,
                'AdminPostalCode'=>@$request->AdminPostalCode,
                'AdminCountry'=>$request->AdminCountry,
                'AdminPhone'=>$request->AdminPhone,
                'AdminEmailAddress'=>$request->AdminEmailAddress,
                'AdminCity'=>@$request->AdminCity, 

                'RegistrantFirstName'=>@$request->RegisterFirstName,
                'RegistrantLastName'=>@$request->RegisterLastName,
                'RegistrantAddress1'=>@$request->RegisterAddress1,
                'RegistrantStateProvince'=>@$request->RegisterStateProvince,
                'RegistrantPostalCode'=>@$request->RegisterPostalCode,
                'RegistrantCountry'=>$request->RegisterCountry,
                'RegistrantPhone'=>@$request->RegisterPhone,
                'RegistrantEmailAddress'=>@$request->RegisterEmailAddress,
                'RegistrantCity'=>@$request->RegisterCity,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                $message = @$response['Errors']['Error'];
                
                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            return ['success'=>true, 'message'=>'OK'];


        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function changeNameservers(){

        $domain = $this->domain;
        $request = $this->request;
        $nameservers = $this->makeNameservers($request, $domain, true);

        $array = explode(',', $nameservers);
        $ns1 = @$array[0];
        $ns2 = @$array[1];
        $ns3 = @$array[2];
        $ns4 = @$array[3];
   
        try{
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.dns.setCustom',
                'ClientIp'=>$this->requestIP,
                'SLD'=>explode('.', $domain->domain)[0], //Top level domain
                'TLD'=>explode('.', $domain->domain)[1], //Second level domain
                'NameServers'=>$nameservers,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                $message = @$response['Errors']['Error'];
                
                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $domain->ns1 = @$ns1; 
            $domain->ns2 = @$ns2; 
            $domain->ns3 = @$ns3; 
            $domain->ns4 = @$ns4; 
            $domain->save(); 
    
            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    public function enableIdProtection(){
        
        $domain = $this->domain;

        try{
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'Namecheap.Whoisguard.enable',
                'ClientIp'=>$this->requestIP,
                'DomainName'=>$domain->domain,
                'ForwardedToEmail'=>$domain->user->email,
                'WhoisGuardid'=>$domain->whois_guard,
            ]);

            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                $message = @$response['Errors']['Error'];
                
                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $domain->id_protection = 1; 
            $domain->save(); 
    
            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }

    }

    public function disableIdProtection(){
        
        $domain = $this->domain;

        try{

            if(!$domain->whois_guard){ //When Whoisguard is empty 
                $getInfo = Http::get($this->url, [
                    'ApiUser'=>$this->username,
                    'ApiKey'=>$this->namecheapAcc->api_key->value,
                    'UserName'=>$this->username,
                    'ClientIp'=>$this->requestIP,
                    'Command'=>'namecheap.domains.getinfo',
                    'DomainName'=>$domain->domain,
                ]);
                $id = xmlToArray(@$getInfo)['CommandResponse']['DomainGetInfoResult']['Whoisguard']['ID']; 
                
                $domain->whois_guard = @$id; 
                $domain->save(); 
            }

            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'Namecheap.Whoisguard.disable',
                'ClientIp'=>$this->requestIP,
                'WhoisGuardid'=>$domain->whois_guard,
            ]);
    
            $response = xmlToArray(@$response); 
            if(@$response['Errors']){
                $message = @$response['Errors']['Error'];
                
                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }

            $domain->id_protection = 0; 
            $domain->save(); 
    
            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

    protected function adminNotification($data, $message){
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user_id;
        $adminNotification->title = gettype($message) == 'array' ? implode('. ', $message) : $message;
        $adminNotification->api_response = 1;
        $adminNotification->click_url = urlPath('admin.order.domain.details', $data->id);
        $adminNotification->save();
    } 

    public function searchDomain(){
        
        $domain = $this->domain;
        $domains = null;
        
        $isSupported = true;
        $sld = getSld($domain);
        $tld = getTld($domain);

        $domainSetup = DomainSetup::active()->with('pricing')->get(['id', 'extension']);
        if($tld && !$domainSetup->where('extension', $tld)->first()){
            $isSupported = false;
        }

        if($this->singleSearch){
            $domains = $domain;
        }else{
            foreach($domainSetup as $data){
                $domains .= "$sld$data->extension,";
            }
        }

        try{
            $response = Http::get($this->url, [
                'ApiUser'=>$this->username,
                'ApiKey'=>$this->namecheapAcc->api_key->value,
                'UserName'=>$this->username,
                'Command'=>'namecheap.domains.check',
                'ClientIp'=>$this->requestIP,
                'DomainList'=>$domains,
            ]);

            $response = xmlToArray(@$response);    
            $result = [];    

            if(@$response['Errors']){ 
                $message = @$response['Errors']['Error'];
                return ['success'=>false ,'message'=>@$message];
            }
            
            if($this->singleSearch){
                $data = @$response['CommandResponse']['DomainCheckResult']['@attributes'];

                $dataDomain = @$data['Domain'];
                $dataTld = getTld($dataDomain);
                $getSetup = $domainSetup->where('extension', $dataTld)->first();

                return [
                    'success'=>true, 
                    'regster_name'=>'namecheap', 
                    'domain'=>$domain, 
                    'sld'=>$sld, 
                    'tld'=>$tld, 
                    'isSupported'=>$isSupported, 
                    'setup'=>$getSetup, 
                    'data'=>@$data
                ];
            }

            foreach(@$response['CommandResponse']['DomainCheckResult'] ?? [] as $index => $data){
                $data = $data['@attributes'];
                $dataDomain = @$data['Domain'];
                $dataTld = getTld($dataDomain);

                $getSetup = $domainSetup->where('extension', $dataTld)->first();

                $result[] = [
                    'match' => 0,
                    'domain' => $dataDomain,
                    'setup' => $getSetup,
                    'available' => @$data['Available'] == "true" ? true : false,
                ];

                if($domain == $dataDomain){
                    $result[$index]['match'] = 999;
                }
            }
          
            return [
                'success'=>true, 
                'regster_name'=>'namecheap', 
                'domain'=>$domain, 
                'sld'=>$sld, 
                'tld'=>$tld, 
                'isSupported'=>$isSupported, 
                'data'=>$result
            ];

        }catch(\Exception  $error){      
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
    }

}

