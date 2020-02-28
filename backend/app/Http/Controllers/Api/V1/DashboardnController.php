<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\BaseModel;
use App\User;

use Event;
use Gate;
use DB;

class DashboardnController extends Controller
{
    private function ControlDashboardAuth($auth)
    {
        Gate::define('dashboardGetData', 'App\Policies\UserPolicy@dashboardGetData');
        if(Gate::denies('dashboardGetData', [$auth]))
            custom_abort('no.auth.for.dashboard');
    }
    
    public function GetData(User $user, $auth)
    {   
        send_log('info', 'Request Dashboard Data');
        
        $this->ControlDashboardAuth($auth);
               
        $data = Event::dispatch('standart.dashboard.getData.requested', [$auth])[0];
        
        send_log('info', 'Response Dashboard Data', [$data]);
        
        return helper('response_success', $data); 
    }
}
