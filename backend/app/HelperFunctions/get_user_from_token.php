<?php

use \App\User;

if($params == 'public') 
    return \Cache::rememberForever('publicUser', function()
    {
        return User::where('state', TRUE)->find(PUBLIC_USER_ID);
    });
    

$id = last(explode('d', $params));
if(!is_numeric($id)) return NULL;

$id = (int)$id;
if($id < 1) return NULL;

$user = User::where('state', TRUE)->find($id);

$user = helper('clear_user_token', $user);
if(!helper('control_token', ['record' => $user, 'token' => $params])) return NULL;

return $user;