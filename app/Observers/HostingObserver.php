<?php

namespace App\Observers;

use App\Models\Hosting;

class HostingObserver
{
    /**
     * Handle the Hosting "created" event.
     *
     * @param  \App\Models\Hosting  $hosting 
     * @return void
     */

    //When creating a new hosting
    public function created(Hosting $hosting){       

        $hosting = Hosting::where('id', $hosting->id)->first(); 

        if(!$hosting->username){
            $hosting->username = $this->makeHostingPanelUsername($hosting);
        }

        if(!$hosting->password){
            $hosting->password = $this->makeHostingPanelPassword(); 
        }

        $hosting->save();
    }

    protected function makeHostingPanelUsername($hosting){

        $server = @$hosting->server;
        $serverGroup = @$server->group;

        if(@$serverGroup->type == 2){ // 2 means Directadmin
            return $this->directadminUsername($hosting);
        }

        $username = 'whmlab';

        if($hosting->domain){
            $username = $hosting->domain;
        }

        $username = preg_replace('/[^A-Za-z\-]/', '', $username);
        $exists = Hosting::where('username', $username)->first('id');

        if($exists){
            $username = $username.$hosting->id;
        }

        return $username;
    }

    protected function directadminUsername($hosting){
        $username = $hosting->domain ?? 'directadmin';

        $username = strtolower($username);
        $username = preg_replace('/[^a-z]/', '', $username);

        $username = substr($username, 0, 10);
        if (strlen($username) < 3) {
            $username = str_pad($username, 3, 'x');
        }

        return $username;
    }

    protected function makeHostingPanelPassword($length = 15){

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!$%';
        $password = '';
        $characterListLength = mb_strlen($characters, '8bit') - 1;

        foreach(range(1, $length) as $i){
            $password .= $characters[random_int(0, $characterListLength)];
        }
        
        return $password;
    }

    /**
     * Handle the Hosting "updated" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function updated(Hosting $hosting)
    {
        //
    }

    /**
     * Handle the Hosting "deleted" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function deleted(Hosting $hosting)
    {
        //
    }

    /**
     * Handle the Hosting "restored" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function restored(Hosting $hosting)
    {
        //
    }

    /**
     * Handle the Hosting "force deleted" event.
     *
     * @param  \App\Models\Hosting  $hosting
     * @return void
     */
    public function forceDeleted(Hosting $hosting)
    {
        //
    }
}






