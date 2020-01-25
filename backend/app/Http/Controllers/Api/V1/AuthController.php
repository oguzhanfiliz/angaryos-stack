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
        
    public function getLoggedInUserInfo($user)
    {
        send_log('info', 'Request For Logged In User Info');
        
        $data =
        [
            'user' => $user->toSafeArray(),
            'menu' => $user->getMenuArray(),
            'auths' => $user->auths,
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
