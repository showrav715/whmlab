<?php

namespace App\HostingModule\Server;

interface HostingManagerInterface{
 
    public function create($hosting);

    public function suspend($data);

    public function unSuspend($hosting);

    public function terminate($hosting);

    public function changePackage($hosting);

    public function changePassword($hosting);

    public function accountSummary($hosting);

    public function loginServer($server);

    public function loginAccount($hosting);

    public function getIP($server);

    public function getPackage($serverGroup);

}
