<?php

namespace App\Listeners;

class MissionSubscriber 
{
    public function DoMission($mission)
    {
        $return = FALSE;
        eval(helper('clear_php_code', $mission->php_code));            
        return $return;
    }
}
