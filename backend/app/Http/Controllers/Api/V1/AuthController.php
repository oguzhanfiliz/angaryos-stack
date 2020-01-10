<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Libraries\SessionLibrary;

use DB;
use Hash;

class AuthController extends Controller
{
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
}
