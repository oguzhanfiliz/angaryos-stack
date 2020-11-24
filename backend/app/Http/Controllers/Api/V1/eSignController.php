<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Cache;
use DB;

class eSignController extends Controller
{
    public function __construct()
    {
        //\Cache::flush();
    }
    
    public function control($user)
    {
        $token = $this->getToken($user);
        
        $eSingCount = $this->getESignCount($token, $user);
        
        return helper('response_success', 
        [
            'eSingCount' => $eSingCount,
            'waitTime' => helper('get_wait_time')
        ]);
    }
    
    private function getToken($user)
    {
        $token = \Request::segment(3);
        
        $key = 'token:'.$token;
        Cache::forget($key);        
        $uId = Cache::remember($key, 60 * 60 * 24 * 4, function() use($user)
        {
            return $user->id;
        });
        
        return $token;
    }
    
    private function getESignCount($token, $user)
    {
        $eSingCount = DB::table('e_signs')
                        ->whereRaw('((sign_at::text = \'\') is not false)')
                        ->where('own_id', $user->id)
                        ->where('state', TRUE)
                        ->count();
        
        $key = 'userToken:'.$token.'.eSingCount';
        Cache::forget($key);
        
        return Cache::remember($key, 60 * 60 * 24 * 4, function() use($eSingCount)
        {
            return $eSingCount;
        });
    }
}
