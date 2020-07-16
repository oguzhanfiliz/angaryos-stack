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
        
        $email = read_from_response_data('get', 'email');
        $password = read_from_response_data('get', 'password');
        
        $session = new SessionLibrary();
        $token = $session->loginAndGetToken($email, $password);
        
        if($token == FALSE)
            return custom_abort('mail.or.password.incorrect');
        
        send_log('info', 'Response Succes Login', $token);
        return helper('response_success', ['token' => $token]);
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
