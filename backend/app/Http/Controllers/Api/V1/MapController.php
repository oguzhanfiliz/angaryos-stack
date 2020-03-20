<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Libraries\SessionLibrary;

use \App\User;

class MapController extends Controller
{
    public function __construct()
    {
        //\Cache::flush();
    }  
    
    private function AddFilterInRequest($request) 
    {
        return $request;
    }
    
    private function GetAllRequest() 
    {
        $temp = \Request::all();
        
        $request = [];
        foreach($temp as $key => $value)
            $request[strtoupper($key)] = $value;
        
        return $request;
    }
    
    private function Control() 
    {
        $request = $this->GetAllRequest();
        $this->ServiceTypeControl($request);
        
        return $request;
    }
    
    private function ServiceTypeControl($request) 
    {
        switch (strtoupper(@$request['SERVICE'])) 
        {
            case 'WMS': break;
            default: custom_abort('undefined.service.type: ' . @$request['SERVICE']);
        }
    }
    
    private function GetUrl($request)
    {
        $url = 'http://geoserver:8080/geoserver/angaryos/';
        $url .= strtolower($request['SERVICE']).'?';
                
        foreach($request as $key => $value)       
            $url .= $key.'='.$value.'&';
        
        return $url;
    }
    
    private function GetImage($url)
    {
        $imginfo = getimagesize( $url );
        header("Content-type: ".$imginfo['mime']);
        return readfile( $url );
    }
    
    public function GetTile($user)
    {
        $request = $this->Control();
        $request = $this->AddFilterInRequest($request);
        
        $url = $this->GetUrl($request);
        return $this->GetImage($url);
    }
}
