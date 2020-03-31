<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Libraries\SessionLibrary;

use DB;
use Cache;
use App\User;

class MapController extends Controller
{
    public function __construct()
    {
        //\Cache::flush();
        //dd(getMemcachedKeys());
    }  
    
    private function AddFilterInRequest($user, $request) 
    {
        $tableName = explode(':', $request['LAYERS'])[1];
        
        if(substr($tableName, 0, 2) == 'v_')
            $tableName = substr($tableName, 2);
        
        $token = \Request::segment(3);
        
        if(!isset($user->auths['filters'][$tableName]['list'])) 
        {
            Cache::remember('userToken:'.$token.'.tableName:'.$tableName.'.mapFilters', 60 * 60, function()
            {
                return 'OK';
            });
            
            return $request;
        }
        
        $filter = '';
        $filterIds = $user->auths['filters'][$tableName]['list'];
        foreach($filterIds as $filterId)
        {
            $sqlCode = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');
            $sqlCode = str_replace('TABLE.', '', $sqlCode); 
            $sqlCode = urlencode($sqlCode);
            
            $filter .= '%20and%20'.$sqlCode;
        }
        $filter = substr($filter, 9);
        
        Cache::remember('userToken:'.$token.'.tableName:'.$tableName.'.mapFilters', 60 * 60, function() use($filter)
        {
            return $filter;
        });
                        
        if(isset($request['CQL_FILTER']))
            $request['CQL_FILTER'] .= '%20and%20'.$filter;
        else
            $request['CQL_FILTER'] = $filter;
        
        return $request;
    }
    
    private function AuthControl($user, $request) 
    {
        if(!isset($request['LAYERS'])) custom_abort ('undefined.LAYERS.data');
        
        $temp = explode(':', $request['LAYERS']);
        if(count($temp) != 2) 
            custom_abort ('undefined.LAYERS.data: ' . $request['LAYERS']);
        
        if(substr($temp[1], 0, 2) == 'v_')
        {
            $layerName = substr($temp[1], 2);
            if(!isset($user->auths['tables'][$layerName]['maps']))
                custom_abort ('no.auth.for.layer: ' . $layerName);

            $temp = $user->auths['tables'][$layerName]['maps'];
            if(!in_array(0, $temp) && !in_array(1, $temp))
                custom_abort ('no.auth.for.layer: ' . $layerName);
        }
        else
        {
            $control = FALSE;
            
            foreach($user->auths['custom_layers'] as $id => $t)
            {
                $layerName = get_attr_from_cache('custom_layers', 'id', $id, 'name');
                $layerName = helper('seo', $layerName);
                
                if($layerName == $temp[1])
                {
                    $control = TRUE;
                    break;
                }
            }
            
            if(!$control) custom_abort ('no.auth.for.layer: ' . $temp[1]);
        }
    }
    
    private function GetAllRequest() 
    {
        $temp = \Request::all();
        
        $request = [];
        foreach($temp as $key => $value)
            $request[strtoupper($key)] = $value;
        
        return $request;
    }
    
    private function Control($user) 
    {
        $request = $this->GetAllRequest();
        $this->AuthControl($user, $request);
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
        $url = 'http://geoserver:8080/geoserver/'.env('GEOSERVER_WORKSPACE', 'angaryos').'/';
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
        $request = $this->Control($user);
        $request = $this->AddFilterInRequest($user, $request);
        
        $url = $this->GetUrl($request);
        return $this->GetImage($url);
    }
}
