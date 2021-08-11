<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Libraries\SessionLibrary;

use \App\User;

use DB;
use Hash;
use Event;

class AuthController extends Controller
{
    use AuthTrait;
    
    public function __construct()
    {
        //\Cache::flush();
    }
    
    public function login()
    {
        send_log('info', 'Request For Login');
        
        $email = read_from_response_data('email');
        $password = read_from_response_data('password');
        
        $session = new SessionLibrary();
        $token = $session->loginAndGetToken($email, $password);
        
        if($token == FALSE)
            return custom_abort('mail.or.password.incorrect');
        
        send_log('info', 'Response Succes Login', $token);
        return helper('response_success', ['token' => $token]);
    }

    public function deviceLogin()
    {
        send_log('info', 'Request For Device Login');

        $type = read_from_response_data('type');
        if(!$type) custom_abort('..no.auth');

        $table = get_attr_from_cache('tables', 'name', $type, '*');
        if(!$table) custom_abort('no.auth.');

        $uniqueColumnId = get_attr_from_cache('columns', 'name', 'device_unique_info', 'id');
        $columnIds = json_decode($table->column_ids);
        if(!in_array($uniqueColumnId, $columnIds)) custom_abort('.no.auth');

        global $pipe;
        $pipe['tableName'] = $table->name;


        $unique = read_from_response_data('unique');
        if(!$unique) custom_abort('no.auth');
        
        $device = get_model_from_cache($table->name, 'device_unique_info', $unique);
        if(!$device) custom_abort('no.auth.');
        if(!$device->state) custom_abort('no.auth..');

        
        if(strlen($device->ip) > 0)
        {
            $control = FALSE;
            foreach(explode(',', $device->ip) as $ip)
            {
                $ip = trim($ip);
                if(strlen($ip) == 0) continue;

                if(strstr($_SERVER['HTTP_X_FORWARDED_FOR'], $ip)) 
                {
                    $control = TRUE;
                    break;
                }
            }

            if(!$control)
            {
                send_log('error', 'Yetkisiz cihaz login isteği'); 
                custom_abort('no.auth...');
            }
        }

        $session = new SessionLibrary();
        $token = $session->deviceLoginAndGetToken($device);
        
        if($token == FALSE)
            return custom_abort('no.auth....');
        
        $temp = $device->toArray();
        $exts = ['password','ip','device_unique_info','tokens', 'public_key'];
        foreach($exts as $e) 
        {
            try { unset($temp[$e]); } catch(\Exception $ee) {}
        }

        send_log('info', 'Response Succes Device Login', $token);
        return helper('response_success', ['token' => $token, 'device' => $temp]);
    }
    
    public function LogOut($user)
    {
        send_log('info', 'Request Logout', $user);
        
        $token = \Request::segment(3);
        
        $tokens = [];
        foreach($user->tokens as $temp)
            if($temp['token'] != $token)
                array_push($tokens, $temp);
        
        $user->tokens = $tokens;
        $user->save();

        send_log('info', 'Logout Success', $token);
        return helper('response_success', 'success');
    }
        
    public function getLoggedInUserInfo($user)
    {
        send_log('info', 'Request For Logged In User Info');
        
        $debugUserIds = json_decode(DEBUG_USER_IDS);
        
        $data =
        [
            'user' => $user->toSafeArray(),
            'menu' => $user->getMenuArray(),
            'map' => $user->getMapArray(),
            'reports' => $user->getReportsArray(),
            'dashboards' => $user->getDashboardArray(),
            'auths' => $user->auths,
            'debug_user' => in_array($user->id, $debugUserIds)
        ];
        
        send_log('info', 'Response Logged In User Info', $data);
        
        return helper('response_success', $data);
    }
    
    public function getUserToken($user, $requestUserId)
    {
        send_log('info', 'Request For Get User Token', [$user->toArray(), $requestUserId]);
        
        if(!isset($user->auths['admin']['userImitation']))
            custom_abort('no.auth');
        
        $requestUser = User::find($requestUserId);
        if(!$requestUser) custom_abort('user.is.passive');
        
        $token = helper('create_user_token', $requestUser);
        
        send_log('info', 'Response For Get User Token', [$token]);
        
        return helper('response_success', ['token' => $token]);
    }
    
    public function assignAuth($user)
    {
        send_log('info', 'Request For Auth Assign', $user);
        
        $this->authControlForAssignAuth();
        
        $params = $this->getValidatedParamsForAssignAuth();
        
        Event::dispatch('auth.assign.requested', [$params]);
        
        send_log('info', 'Response For Auth Assign');
        
        return helper('response_success', 'success');
    }
    
}
