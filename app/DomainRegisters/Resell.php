<?php

namespace App\DomainRegisters;

use App\Models\AdminNotification;
use App\Models\DomainRegister;
use App\Models\DomainSetup;
use Illuminate\Support\Facades\Http;

class Resell{

    public $url;
    public $tld;
    public $domain; 
    public $request;
    public $resellAcc;
    public $register;
    public $singleSearch;

    public function __construct($domain){	
		$this->domain = $domain;
        $this->register = DomainRegister::where('alias', 'Resell')->firstOrFail();
        $this->url = $this->register->test_mode ? 'https://test.httpapi.com' : 'https://httpapi.com';
        $this->resellAcc = $this->register->params;
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
        $user = $domain->user;
        $request = $this->request;

        $nameservers = $this->makeNameservers($request, $domain);
        
        $array = explode(',', $nameservers);
        $ns1 = @$array[0];
        $ns2 = @$array[1];
        $ns3 = @$array[2];
        $ns4 = @$array[3];
    
        $nameservers = implode('&ns=', $array);
 
        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $dialCode = $countryData[@$user->country_code]->dial_code;
        $countCode = strlen($dialCode);
        $phoneWithoutCode = substr($user->mobile, $countCode);

        try{   
 
            //Get user details if the user exists
            $getUser = Http::get($this->url.'/api/customers/details.json', [
                'auth-userid'=>$this->resellAcc->auth_user_id->value,
                'api-key'=>$this->resellAcc->api_key->value,
                'username'=>$user->email,
            ]);
            $getUser = json_decode($getUser);
          
            //If customerid is empty
            if(!$domain->customer_id && @$getUser->customerid){
                $domain->customer_id = @$getUser->customerid; 
                $domain->save(); 
            }

            //If doesn't get the user from $getUser
            if(!@$getUser->username){

                //Trying to create / register a new user
                $createUser = Http::post($this->url.'/api/customers/v2/signup.xml?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&username='.$user->email.'&passwd=Passw@rd123&name='.$user->fullname.'&company=CompanyName&address-line-1='.@$user->address.'&city='.@$user->city.'&state='.@$user->state.'&country='.@$user->country_code.'&zipcode='.@$user->zip.'&phone-cc='.$dialCode.'&phone='.$phoneWithoutCode.'&lang-pref=en');
              
                $createUser = @xmlToArray(@$createUser);  
                if(@$createUser['status'] == 'ERROR'){ 
                    $this->adminNotification($domain, @$createUser['message']);
                    return ['success'=>false, 'message'=>@$createUser['message']];
                }

                $domain->customer_id = @$createUser[0]; 
                $domain->save(); 
            }

            if(@$getUser->status == 'ERROR'){
                $this->adminNotification($domain, $getUser->message);
                return ['success'=>false, 'message'=>$getUser->message];
            }

            //Get user contact details
            $getContact = Http::get($this->url.'/api/contacts/details.json', [
                'auth-userid'=>$this->resellAcc->auth_user_id->value,
                'api-key'=>$this->resellAcc->api_key->value,
                'contact-id'=>$domain->contact_id,
            ]);
            $getContact = json_decode($getContact);
            
            //If doesn't get the user contact details from $getContact
            if(!@$getContact->contactid){
              
                //Trying to create a new contact info
                $createContact = Http::post($this->url.'/api/contacts/add.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&name='.$user->fullname.'&company=CompanyName&email='.$user->email.'&address-line-1='.@$user->address.'&address-line-2='.@$user->address.'&city='.@$user->city.'&country='.@$user->country_code.'&zipcode='.@$user->zip.'&phone-cc='.$dialCode.'&phone='.$phoneWithoutCode.'&customer-id='.$domain->customer_id.'&type=Contact');
                
                if(@$createContact->status != 'ERROR'){
                    $domain->contact_id = @$createContact ?? 0; 
                    $domain->save(); 
                }
            }

            if(@$getContact->status == 'ERROR'){
                $this->adminNotification($domain, $getContact->message);
                return ['success'=>false, 'message'=>$getContact->message];
            }

            $protection = $domain->id_protection ? 'true' : 'false';
            
            //Register API, this is the main API endpoint of this method
            $response = Http::post($this->url.'/api/domains/register.xml?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&domain-name='.$domain->domain.'&years='.$domain->reg_period.'&ns='.$nameservers.'&customer-id='.$domain->customer_id.'&reg-contact-id='.$domain->contact_id.'&admin-contact-id='.$domain->contact_id.'&tech-contact-id='.$domain->contact_id.'&billing-contact-id='.$domain->contact_id.'&invoice-option=KeepInvoice&purchase-privacy='.$protection);
            
            $response = xmlToArray(@$response); 
            if(@$response['entry'][0]['string'][1] == 'error' || @$response['status'] == 'ERROR'){ 
                $message = @$response['entry'][1]['string'][1] ?? $response['message'];

                $this->adminNotification($domain, @$message);
                return ['success'=>false, 'message'=>@$message];
            }
         
            $domain->ns1 = @$ns1; 
            $domain->ns2 = @$ns2; 
            $domain->ns3 = @$ns3; 
            $domain->ns4 = @$ns4; 
            $domain->status = 1; 
            $domain->resell_order_id = @$response['hashtable']['string'][7]; 
            $domain->save(); 

            //If the request for ID privacy protection
            if($domain->id_protection){
                Http::post($this->url.'/api/domains/purchase-privacy.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&invoice-option=NoInvoice');
            }

            return ['success'=>true, 'message'=>'OK'];

        }catch(\Exception  $error){
            return ['success'=>false, 'message'=>$error->getMessage()];
        }
	}

    public function renew(){
        $domain = $this->domain;
        $request = $this->request;
        $renewYear = $request ? $request->renew_year : $domain->reg_period;

        try{
            //Try to get domain details when resell_order_id is empty
            if(!$domain->resell_order_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain, 
                    'options'=>'OrderDetails'
                ]);

                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }

            $response = Http::post($this->url.'/api/domains/renew.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&years='.$renewYear.'&exp-date=1279012036&invoice-option=NoInvoice');

            $response = json_decode(@$response); 
            if(@$response->status == 'ERROR'){
                $message = @$response->message;

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

            //Trying to get domain details when contact_id is empty
            if(!$domain->contact_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'ContactIds'
                ]);

                $domain->contact_id = @json_decode($details)->registrantcontactid;
                $domain->save();
            }

            $response = Http::get($this->url.'/api/contacts/details.json', [
                'auth-userid'=>$this->resellAcc->auth_user_id->value,
                'api-key'=>$this->resellAcc->api_key->value,
                'contact-id'=>$domain->contact_id
            ]);

            $response = @json_decode(@$response);
            if(@$response->status == 'ERROR'){
                $message = @$response->message;

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

            $response = Http::post($this->url.'/api/contacts/modify.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&contact-id='.$domain->contact_id.'&name='.$request->name.'&company=CompanyName&email='.$request->email.'&address-line-1='.$request->address1.'&address-line-2='.$request->address2.'&city='.$request->city.'&country='.$request->country.'&zipcode='.$request->zip.'&phone-cc='.$request->telephonecc.'&phone='.$request->telephone);

            $response = json_decode(@$response); 
            if(@$response->status == 'ERROR'){
                $message = @$response->message;

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

        $nameservers = implode('&ns=', $array);

        try{    
           
            //Trying to get domain details when resell_order_id is empty
            if(!$domain->resell_order_id){ 
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'OrderDetails'
                ]); 
           
                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }
          
            $response = Http::post($this->url.'/api/domains/modify-ns.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&ns='.$nameservers);

            $response = json_decode(@$response); 
            if(@$response->status == 'ERROR'){
                $message = @$response->message;

                $this->adminNotification($domain, $message);
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

            //Trying to get domain details when resell_order_id is empty
            if(!$domain->resell_order_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'OrderDetails'
                ]);

                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }

            $response = Http::post($this->url.'/api/domains/modify-privacy-protection.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&protect-privacy=true&reason=PrivacyProtect');

            $response = json_decode(@$response);
            if(@$response->status == 'ERROR'){
                $message = @$response->message;

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

            //Trying to get domain details when resell_order_id is empty
            if(!$domain->resell_order_id){
                $details = Http::get($this->url.'/api/domains/details-by-name.json', [
                    'auth-userid'=>$this->resellAcc->auth_user_id->value,
                    'api-key'=>$this->resellAcc->api_key->value,
                    'domain-name'=>$domain->domain,
                    'options'=>'OrderDetails'
                ]);

                $domain->resell_order_id = @json_decode($details)->orderid;
                $domain->save();
            }

            $response = Http::post($this->url.'/api/domains/modify-privacy-protection.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&order-id='.$domain->resell_order_id.'&protect-privacy=false&reason=PrivacyProtect');

            $response = json_decode(@$response);
            if(@$response->status == 'ERROR'){
                $message = @$response->message;

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

        $isSupported = true;
        $tlds = null;

        $sld = getSld($domain);
        $tld = getTld($domain);

        $domainSetup = DomainSetup::active()->with('pricing')->get(['id', 'extension']);
        if($tld && !$domainSetup->where('extension', $tld)->first()){
            $isSupported = false;
        }

        if($this->singleSearch){
            $tlds = substr($tld, 1);
        }else{
            $lastIndex = count($domainSetup) - 1;
            foreach ($domainSetup as $index => $data) {
                $tlds .= substr($data->extension, 1);
            
                // Append '&tlds=' only if the current element is not the last one
                if ($index !== $lastIndex) {
                    $tlds .= '&tlds=';
                }
            }
        }

        try{
            $response = Http::get($this->url.'/api/domains/available.json?auth-userid='.$this->resellAcc->auth_user_id->value.'&api-key='.$this->resellAcc->api_key->value.'&domain-name='.$sld.'&tlds='.$tlds);

            $response = json_decode(@$response, true); 
            if(@$response['status'] == 'ERROR'){
                return ['success'=>false ,'message'=>@$response['message']];
            }

            if($this->singleSearch){

                $dataTld = getTld(key($response));
                $getSetup = $domainSetup->where('extension', $dataTld)->first();

                return [
                    'success'=>true, 
                    'regster_name'=>'resell', 
                    'domain'=>$domain, 
                    'sld'=>$sld, 
                    'tld'=>$tld, 
                    'isSupported'=>$isSupported, 
                    'setup'=>$getSetup, 
                    'data'=>$response
                ];
            }

            $result = [];  
            $index = 0;

            foreach($response ?? [] as $dataDomain => $data){
                $dataTld = getTld($dataDomain);
                $getSetup = $domainSetup->where('extension', $dataTld)->first();

                $result[] = [
                    'match' => 0,
                    'domain' => $dataDomain,
                    'setup' => $getSetup,
                    'available' => @$data['status'] == "available" ? true : false,
                ];

                if($domain == $dataDomain){
                    $result[$index]['match'] = 999;
                }

                $index++;
            }

            return [
                'success'=>true, 
                'regster_name'=>'resell', 
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


