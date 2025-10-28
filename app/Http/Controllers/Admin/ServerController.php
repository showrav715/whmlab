<?php

namespace App\Http\Controllers\Admin;

use App\HostingModule\HostingManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\ServerGroup;
use Illuminate\Support\Facades\Validator;

class ServerController extends Controller{
     
    public function groupsServer(){ 
        $pageTitle = 'Server Groups';
        $groups = ServerGroup::paginate(getPaginate());
        return view('admin.server.group',compact('pageTitle', 'groups')); 
    }
 
    public function addGroupServer(Request $request){ 
      
        $request->validate([
    		'name' => 'required|max:255',
    		'type' => 'required|in:1,2,3',
    	]);

        $group = new ServerGroup();
        $group->name = $request->name;
        $group->type = $request->type;
        $group->save();

        $notify[] = ['success', 'Server group added successfully'];
	    return back()->withNotify($notify);
    }  
 
    public function updateGroupServer(Request $request){
 
        $request->validate([
    		'id' => 'required|integer',
    		'name' => 'required|max:255',
    		'type' => 'required|in:1,2,3',
    	]);

        $group = ServerGroup::findOrFail($request->id);
        $group->name = $request->name;
        $group->type = $request->type;
        $group->save();

        $notify[] = ['success', 'Server group updated successfully'];
	    return back()->withNotify($notify);
    } 

    public function servers(){
        $pageTitle = 'All Servers';
        $servers = Server::with('group')->paginate(getPaginate());
        return view('admin.server.all',compact('pageTitle', 'servers'));
    } 
    
    public function addServerPage(){
        $pageTitle = 'New Server';
        $groups = ServerGroup::active()->orderBy('id', 'DESC')->get();
        return view('admin.server.add',compact('pageTitle', 'groups'));
    }
 
    public function addServer(Request $request){

        $request->validate([
    		'name' => 'required|max:255',
    		'host' => 'required',
    		'protocol' => 'required|in:https://,http://',
    		'port' => 'required',
    		'username' => 'required',
    		'password' => 'required',
    		'api_token' => 'nullable',
    		'security_token' => 'nullable',
    		'server_group_id' => 'required',
            'ns1' => 'required',
    		'ns1_ip' => 'required',
    		'ns2' => 'required',
    		'ns2_ip' => 'required', 
    	]);

        $serverGroup = ServerGroup::active()->findOrFail($request->server_group_id);
        $hostname = $request->protocol.$request->host.':'.$request->port;

        $server = new Server();
        $server->type = $serverGroup->getType; 
        $server->server_group_id = $serverGroup->id;

        $server->protocol = $request->protocol;
        $server->host = $request->host;
        $server->port = $request->port;

        $server->name = $request->name;
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token;
        $server->security_token = $request->security_token;

        $hostingManager = HostingManager::init($serverGroup);
        $execute = $hostingManager->loginServer($server);

        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $server->ns1 = $request->ns1;
        $server->ns1_ip = $request->ns1_ip;
        $server->ns2 = $request->ns2;
        $server->ns2_ip = $request->ns2_ip;
        $server->ns3 = $request->ns3;
        $server->ns3_ip = $request->ns3_ip;
        $server->ns4 = $request->ns4;
        $server->ns4_ip = $request->ns4_ip;

        $server->ip_address = $hostingManager->getIp($server);
        $server->status = 1;
        $server->save();

        $notify[] = ['success', 'Server added successfully'];
	    return redirect()->route('admin.server.edit.page', $server->id)->withNotify($notify);
    }

    public function editServerPage($id){
        $server = Server::findOrFail($id);
        $pageTitle = 'Update Server';
        $groups = ServerGroup::active()->orderBy('id', 'DESC')->get();
        return view('admin.server.edit',compact('pageTitle', 'groups', 'server'));
    } 

    public function updateServer(Request $request){

        $request->validate([
    		'id' => 'required|integer',
    		'name' => 'required|max:255',
            'host' => 'required',
            'protocol' => 'required|in:https://,http://',
            'port' => 'required',
    		'username' => 'required',
    		'password' => 'required',
    		'api_token' => 'nullable',
            'security_token' => 'nullable',
    		'server_group_id' => 'required',
            'ns1' => 'required',
    		'ns1_ip' => 'required',
    		'ns2' => 'required',
    		'ns2_ip' => 'required', 
    	]);

        $server = Server::findOrFail($request->id);
        $serverGroup = ServerGroup::findOrFail($request->server_group_id);

        $hostname = $request->protocol.$request->host.':'.$request->port;
        $server->server_group_id = $serverGroup->id;

        $server->protocol = $request->protocol;
        $server->host = $request->host;
        $server->port = $request->port;

        $server->name = $request->name;
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;
        $server->api_token = $request->api_token;
        $server->security_token = $request->security_token;

        $execute = HostingManager::init($serverGroup)->loginServer($server);
        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        $server->ns1 = $request->ns1;
        $server->ns1_ip = $request->ns1_ip;
        $server->ns2 = $request->ns2;
        $server->ns2_ip = $request->ns2_ip;
        $server->ns3 = $request->ns3;
        $server->ns3_ip = $request->ns3_ip;
        $server->ns4 = $request->ns4;
        $server->ns4_ip = $request->ns4_ip;

        $server->ip_address = $request->ip_address;
        $server->save();

        $notify[] = ['success', 'Server updated successfully'];
	    return back()->withNotify($notify);
    } 
 
    public function testConnection(Request $request){

        $validator = Validator::make($request->all(), [
            'protocol' => 'required|in:https://,http://',
    		'host' => 'required',
    		'port' => 'required',

    		'username' => 'required',
    		'password' => 'required',

    		'server_group_id' => 'required',
        ]);

        if (!$validator->passes()) {
            $notify[] = $validator->errors();
            return ['success'=>false, 'error'=>$notify];
        }

        $serverGroup = ServerGroup::active()->find($request->server_group_id);
        if (!$serverGroup) {
            $notify[] = 'Server group not found';
            return ['success'=>false, 'error'=> $notify];
        }

        $hostname = $request->protocol.$request->host.':'.$request->port;

        // Temporary server object for test connection
        $server = new Server();
        $server->hostname = $hostname;
        $server->username = $request->username;
        $server->password = $request->password;

        $execute = HostingManager::init($serverGroup)->loginServer($server);
        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }
        
        return [
            'success'=>true
        ];
    }

    public function serverLogin($id){

        $server = Server::with('group')->findOrFail($id);
        $serverGroup = $server->group;

        $execute = HostingManager::init($serverGroup)->loginServer($server);
        if(!$execute['success']){
            $notify[] = ['error', $execute['message']];
            return back()->withNotify($notify);
        }

        return back()->with('loginUrl', $execute['url']);
    }

    public function groupServerStatus($id){
        return ServerGroup::changeStatus($id);
    }

    public function serverStatus($id){
        return Server::changeStatus($id);
    }

} 