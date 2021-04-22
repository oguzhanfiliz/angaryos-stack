<?php

namespace App\Libraries;

use App\User;
use Hash;
use DB;

class SessionLibrary 
{
    public function loginAndGetToken($email, $password) 
    {
        $user = User::where('email', $email)->first();
        if($user == NULL)
        {
            $user = User::where('tc', $email)->first();
            if($user == NULL) return FALSE;
        }
        
        if(!Hash::check($password, $user->password)) return FALSE;
        
        $user = helper('clear_user_token', $user);
        $token = helper('create_user_token', $user);
        
        return $token;
    }

    public function deviceLoginAndGetToken($device) 
    {
        if(strlen($device->password) > 0)
        {
            $password = read_from_response_data('password');
            if(!$password) custom_abort('no.auth');

            if(!Hash::check($password, $device->password))
                custom_abort('no.auth');
        }
        
        $device = helper('clear_device_token', $device);
        $token = helper('create_device_token', $device);
        
        return $token;
    }
}