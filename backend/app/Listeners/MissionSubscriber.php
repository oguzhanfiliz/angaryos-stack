<?php

namespace App\Listeners;

class MissionSubscriber 
{
    public function DoMission($mission)
    {
        $requests = \Request::all();
        
        $return = NULL;
        eval(helper('clear_php_code', $mission->php_code));            
        return $return;
    }
}
