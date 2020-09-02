<?php

namespace App\Http\Controllers\Api\V1;

use Gate;
use DB;

trait AuthTrait
{    
    private function authControlForAssignAuth()
    {
        Gate::define('assignAuth', 'App\Policies\UserPolicy@assignAuth');
        if(Gate::denies('assignAuth'))
            custom_abort('no.auth.for.assgin.auth');
    }
    
    private function getParamFromRequest($name, $json = FALSE)
    {
        $param = read_from_response_data($name, $json);
        if($param === NULL) custom_abort ('no.parameter.'.$name);
        
        return $param;
    }
    
    private function getValidatedParamsForAssignAuth()
    {
        $department_ids = $this->getParamFromRequest('department_ids', TRUE);
        
        
        $auth_id = (int)$this->getParamFromRequest('auth_id');
        $all_user = $this->getParamFromRequest('all_user') == '1';
        
        $user_ids = $this->getParamFromRequest('user_ids', TRUE);
        $department_ids = $this->getParamFromRequest('department_ids', TRUE);
        $auths = $this->getParamFromRequest('auths', TRUE);
        
        $auth = get_attr_from_cache('auth_groups', 'id', $auth_id, '*');
        if($auth == NULL) custom_abort ('no.auth.group');
        
        return [
            'auth_id' => $auth_id,
            'all_user' => $all_user,
            'user_ids' => $user_ids,
            'department_ids' => $department_ids,
            'auths' => $auths
        ];
    }
}
