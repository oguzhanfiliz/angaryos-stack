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
        if($user == NULL) return FALSE;
        
        if(!Hash::check($password, $user->password)) return FALSE;
        
        $user = helper('clear_user_token', $user);
        $token = helper('create_user_token', $user);
        
        return $token;
    }
}