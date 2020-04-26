<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class MapController extends Controller
{
    use MapTrait;

    public function __construct()
    {
        //\Cache::flush();
    }  
    
    public function GetData($user)
    {
        send_log('info', 'Request Map Tile Or WFS Data');

        $request = $this->Control($user);
        $request = $this->AddFilterInRequest($user, $request);
        
        $url = $this->GetUrl($request);      
        return $this->ProxyToUrl($request, $url);        
    }

    public function TranslateKmzOrKmlToJson($user)
    {
        send_log('info', 'Request Translate Kmz Or Kml File To Json');

        $this->UserKmzUploadAuthControl($user);
        
        $file = \Request::file('file');
        $features = $this->GetFeaturesFromFile($user, $file);  
        $tree = $this->ConvertToTreeFromFeatures($features);

        send_log('info', 'Response Translate Kmz Or Kml File To Json', $tree);

        return helper('response_success', $tree);
    }
    
    public function GetSubTables($user, $upTableName, $type)
    {
        send_log('info', 'Request Sub Tables '.$upTableName.':'.$type);
        
        $this->userMapAuthControl($user);
        $this->geoTypeControl($type);
        
        $subTables = $this->GetUserSubTables($user, $upTableName, $type);
        
        send_log('info', 'Response Sub Tables ', json_encode($subTables));

        return helper('response_success', $subTables);
    }
}
