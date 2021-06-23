<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\BaseModel;
use App\User;

use Event;
use Gate;
use DB;

class MissionController extends Controller
{
    private function ControlMissionAuth($mission)
    {
        Gate::define('missionTrigger', 'App\Policies\UserPolicy@missionTrigger');
        if(Gate::denies('missionTrigger', [$mission]))
            custom_abort('no.auth.for.mission.trigger');
    }
    
    public function DoMission(User $user, BaseModel $mission)
    {   
        send_log('info', 'Request Mission Trigger', $mission);
        
        $this->ControlMissionAuth($mission);
                
        $data = Event::dispatch('standart.mission.trigger.requested', [$mission])[0];
        
        send_log('info', 'Response Data For Mission Trigger', [$data]);
        
        return helper('response_success', $data); 
    }
}
