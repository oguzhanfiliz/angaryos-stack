<?php

namespace App\Listeners;

use App\Libraries\DashboardLibrary;

class DashboardSubscriber 
{
    public function getData($auth)
    {
        $auth = explode(':', $auth);
        
        $helper = new DashboardLibrary();
        return $helper->{$auth[1]}($auth[2], $auth[3]);
    }
}
