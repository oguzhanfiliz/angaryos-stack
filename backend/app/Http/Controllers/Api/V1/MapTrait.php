<?php

namespace App\Http\Controllers\Api\V1;

use DB;
use Cache;
use Storage;

trait MapTrait
{
    private function GetTableNameAndCacheNameFromRequestWMS($request)
    {
        $tableName = explode(':', $request['LAYERS'])[1];
        
        if(substr($tableName, 0, 2) == 'v_')
            $tableName = substr($tableName, 2);
        
        return ['tableName' => $tableName, 'cacheName' => $tableName];
    }
    
    private function GetTableNameAndCacheNameFromRequestWFS($request)
    {
        $seoName = explode(':', $request['TYPENAME'])[1];
        $table =$this->getTableFromCustomLayerSeoName($seoName);
        
        return ['tableName' => $table->name, 'cacheName' => $seoName];
    }
    
    private function GetTableNameAndCacheNameFromRequest($request)
    {
        $type = strtolower($request['SERVICE']);
        switch($type)
        {
            case 'wms': return $this->GetTableNameAndCacheNameFromRequestWMS($request);
            case 'wfs': return $this->GetTableNameAndCacheNameFromRequestWFS($request);
            default: dd('buraya hiç düşmemeli!');
        }
    }
    
    private function AddFilterInRequest($user, $request) 
    {
        $names = $this->GetTableNameAndCacheNameFromRequest($request);   
        $token = \Request::segment(3);
        
        if(!isset($user->auths['filters'][$names['tableName']]['list'])) 
        {
            Cache::remember('userToken:'.$token.'.tableName:'.$names['cacheName'].'.mapFilters', 60 * 60, function()
            {
                return 'OK';
            });
            
            return $request;
        }
        
        $filter = '';
        $filterIds = $user->auths['filters'][$names['tableName']]['list'];
        foreach($filterIds as $filterId)
        {
            $sqlCode = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');
            $sqlCode = str_replace('TABLE.', '', $sqlCode); 
            $sqlCode = urlencode($sqlCode);
            
            $filter .= '%20and%20'.$sqlCode;
        }
        $filter = substr($filter, 9);
        
        Cache::remember('userToken:'.$token.'.tableName:'.$names['cacheName'].'.mapFilters', 60 * 60, function() use($filter)
        {
            return $filter;
        });
        
        if(isset($request['CQL_FILTER']))
            $request['CQL_FILTER'] .= '%20and%20'.$filter;
        else
            $request['CQL_FILTER'] = $filter;
        
        return $request;
    }

    private function UserMapAuthControl($user)
    {
        if(!isset($user->auths['map'][0][0]))
            custom_abort("no.map.auth");
    }

    private function UserKmzUploadAuthControl($user)
    {
        $this->UserMapAuthControl($user);
        
        if(!isset($user->auths['map']['kmz']['upload']))
            custom_abort("no.map.kmz.upload.auth");
    }
    
    private function GetDataAuthControl($user, $request)
    {
        $this->UserMapAuthControl($user);

        $type = strtolower($request['SERVICE']);
        
        switch($type)
        {
            case 'wms': return $this->AuthControlWms($user, $request);
            case 'wfs': return $this->AuthControlWfs($user, $request);
            default: custom_abort ('undefined.service.type.'.$type);
        }
    }
    
    private function getTableFromCustomLayerSeoName($seoName)
    {
        $key = 'customLayerSeoName:'.$seoName.'|returnData:table_id';
        $tableId = (int)Cache::rememberForever($key, function() use($seoName)
        {      
            $customLayers = DB::table('custom_layers')->get();
            foreach($customLayers as $customLayer)
                if(helper('seo', $customLayer->name) == $seoName)
                    return $customLayer->table_id;
        });        
        if($tableId == 0) custom_abort ('undefined.layer_name: ' . $seoName);
        
        $table = get_attr_from_cache('tables', 'id', $tableId, '*');
        if(!$table) custom_abort ('undefined.layer_name: ' . $seoName);
        
        return $table;
    }
    
    private function AuthControlWfs($user, $request) 
    {
        if(!isset($request['TYPENAME'])) custom_abort ('undefined.TYPENAME.data');
        
        $temp = explode(':', $request['TYPENAME']);
        if(count($temp) != 2) 
            custom_abort ('undefined.LAYERS.data: ' . $request['LAYERS']);
        
        $table = $this->getTableFromCustomLayerSeoName($temp[1]);
        
        if(!isset($user->auths['tables'][$table->name]['maps']))
            custom_abort ('no.auth.for.layer: ' . $layerName);

        $temp = $user->auths['tables'][$table->name]['maps'];
        if(!in_array(0, $temp) && !in_array(1, $temp))
            custom_abort ('no.auth.for.layer: ' . $layerName);
    }
    
    private function AuthControlWms($user, $request) 
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
        $this->GetDataAuthControl($user, $request);
        $this->ServiceTypeControl($request);
        
        return $request;
    }
    
    private function ServiceTypeControl($request) 
    {
        $type = strtolower(strtoupper(@$request['SERVICE']));
        switch ($type) 
        {
            case 'wms': break;
            case 'wfs': break;
            default: custom_abort('undefined.service.type: ' . $type);
        }
    }
    
    private function GetUrl($request)
    {
        $url = 'http://geoserver:8080/geoserver/'.env('GEOSERVER_WORKSPACE', 'angaryos').'/';
        $url .= strtolower($request['SERVICE']).'?';
                
        foreach($request as $key => $value) 
        {
            if($key != 'CQL_FILTER')
                $value = urlencode($value);
            
            $url .= $key.'='.$value.'&';
        }
        
        return $url;
    }
    
    private function GetImage($url)
    {
        $imginfo = getimagesize($url);
        
        header("Content-type: ".$imginfo['mime']);
        
        return readfile($url);
    }
    
    private function GetJsonData($url)
    {
        return file_get_contents($url);
    }
    
    private function ProxyToUrl($request, $url)
    {
        $type = strtolower($request['SERVICE']);
        switch($type)
        {
            case 'wms': return $this->GetImage($url);
            case 'wfs': return $this->GetJsonData($url);
        }
    }


    private function MoveUploadedFileToTempFolder($user, $file)
    {
        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        $tempFolder = 'temps/';
        $fileName = $user->id.'_'.$file->getClientOriginalName();
        $path = $tempFolder.$fileName;

        $file->move($tempFolder, $fileName);

        chmod($path, 0777);

        return $path;
    }

    private function ExportKmlFromKmz($path)
    {
        $temp = explode('/', $path);
        $fileName = $temp[count($temp) - 1];
        $temp[count($temp) - 1] = str_replace('.kmz', '', $fileName);
        $exportPath = implode('/', $temp);

        $zip = new \ZipArchive;
        if ($zip->open($path) === TRUE) 
        {
            $zip->extractTo($exportPath);
            $zip->close();
        } 
        else
            custom_abort('can.not.open.kmz.file');

        return $exportPath . '/doc.kml';
    }

    private function GetDataArrayFromKmlOrKmzFile($path)
    {
        $temp = explode('.', $path);
        if(last($temp) == 'kmz')
            $path = $this->ExportKmlFromKmz($path);
        
        $xml_string = file_get_contents($path);
        $xml = simplexml_load_string($xml_string);
        $obj = json_encode($xml);
        return json_decode($obj, TRUE);
    }

    private function GetFeatureObjectFromKmlFeaturePoint($temp, $feature)
    {
        $temp['type'] = 'point';
        $temp['orgCoords'] = trim($feature['Point']['coordinates']);

        $coords = explode(',', $temp['orgCoords']);
        $temp['wkt'] = 'POINT('.$coords[0].' '.$coords[1].')';
        
        return $temp;
    }

    private function GetFeatureObjectFromKmlFeatureLineString($temp, $feature)
    {
        if(!isset($feature['LineString']['coordinates'])) return NULL;
            
        $temp['type'] = 'linestring';
        $temp['orgCoords'] = trim($feature['LineString']['coordinates']);
        
        $temp['wkt'] = 'LINESTRING(';
        $coords = explode(' ', $temp['orgCoords']);
        foreach($coords as $coord)
        {
            $point = explode(',', $coord);
            $temp['wkt'] .= $point[0] . ' ' . $point[1] . ', ';
        }

        if(count($coords) > 0)
            $temp['wkt'] = substr($temp['wkt'], 0, -2);

        $temp['wkt'] .= ')';

        return $temp;
    }

    private function GetFeatureObjectFromKmlFeaturePolygon($temp, $feature)
    {
        $coords = $feature['Polygon']['outerBoundaryIs'];
        if(is_array($coords)) $coords = $coords['LinearRing']['coordinates'];

        $temp['type'] = 'polygon';
        $temp['orgCoords'] = trim($coords);
        
        $temp['wkt'] = 'POLYGON((';
        $coords = explode(' ', $temp['orgCoords']);
        foreach($coords as $coord)
        {
            $point = explode(',', $coord);
            $temp['wkt'] .= $point[0] . ' ' . $point[1] . ', ';
        }

        if(count($coords) > 0)
            $temp['wkt'] = substr($temp['wkt'], 0, -2);

        $temp['wkt'] .= '))';

        return $temp;
    }

    private function GetFeatureObjectFromKmlFeatureDirectCoordinates($temp, $feature)
    {
        $temp['coords'] = trim($feature['coordinates']);
        
        if(strstr($feature['coordinates'], ' ')) $temp['type'] = 'linestring';
        else $temp['type'] = 'point';
        dd($temp);
        return $temp;
    }

    private function GetFeatureObjectFromKmlFeatureOuterBoundaryIs($temp, $feature)
    {
        $temp['coords'] = trim($feature['outerBoundaryIs']['LinearRing']['coordinates']);
        $temp['type'] = 'polygon';
        dd($temp);
        return $temp;
    }

    private function GetFeatureObjectFromKmlFeature($layerName, $feature)
    {
        $temp = [];
        //$temp['feature'] = $feature;
        $temp['layerName'] = $layerName;

        $temp['name'] = '';
        if(isset($feature['name'])) $temp['name'] = $feature['name'];

        if(isset($feature['Point']))
            return $this->GetFeatureObjectFromKmlFeaturePoint($temp, $feature);
        else if(isset($feature['LineString']))
            return $this->GetFeatureObjectFromKmlFeatureLineString($temp, $feature);
        else if(isset($feature['Polygon']))
            return $this->GetFeatureObjectFromKmlFeaturePolygon($temp, $feature);
        else if(isset($feature['coordinates']))
            return $this->GetFeatureObjectFromKmlFeatureDirectCoordinates($temp, $feature);
        else if(isset($feature['outerBoundaryIs']))
            return $this->GetFeatureObjectFromKmlFeatureOuterBoundaryIs($temp, $feature);
        else return NULL;
    }

    private function GetFeaturesFromFile($user, $file)
    {   
        $path = $this->MoveUploadedFileToTempFolder($user, $file);
        $data = $this->GetDataArrayFromKmlOrKmzFile($path);

        $data = $data['Document']['Folder'];
        if(isset($data['name'])) $temp = [$data];

        $features = [];
        foreach($data as $item)
        {  
            $layerName = $item['name'];
            $tempData = $item['Placemark'];
            foreach($tempData as $feature)
            {
                $temp = $this->GetFeatureObjectFromKmlFeature($layerName, $feature);
                if($temp == NULL) continue;

                array_push($features, $temp);
            }
        }

        return $features;
    }

    private function ConvertToTreeFromFeatures($features)
    {
        $tree = [];
        foreach($features as $feature)
        {
            if(!isset($tree[$feature['layerName']])) 
                $tree[$feature['layerName']] = [];

            if(!isset($tree[$feature['layerName']][$feature['type']])) 
                $tree[$feature['layerName']][$feature['type']] = [];

            array_push($tree[$feature['layerName']][$feature['type']], $feature);
        }

        return $tree;
    }
    
    private function geoTypeControl($type)
    {
        $types = ['point', 'linestring', 'polygon'];
        if(!in_array($type, $types))
            custom_abort ('invalid.geo.type:'.$type);
    }


    private function GetUserSubTables($user, $upTableName, $type)
    {
        $upTable = get_attr_from_cache('tables', 'name', $upTableName, '*');
        $subTables = DB::table('sub_tables')
                            ->whereRaw('table_ids @> \''.$upTable->id.'\'::jsonb or table_ids @> \'"'.$upTable->id.'"\'::jsonb')
                            ->get();
        
        $return = [];
        foreach($subTables as $subTable)
        {
            $table = get_model_from_cache('tables', 'id', $subTable->sub_table_id);
            $columns = $table->getRelationData('column_ids');
            foreach($columns as $column)
            {
                $columnTypeName = $column->getRelationData('column_db_type_id')->name;
                if(!strstr($columnTypeName, $type)) continue;
                
                $srid = $column->srid;
                if(strlen($srid) == 0) $srid = DB_PROJECTION;
                
                array_push($return, 
                [
                    'tableId' => $table->id,
                    'tableName' => $table->name,
                    'tableDisplayName' => $table->display_name,
                    'columnid' => $column->id,
                    'columnName' => $column->name,
                    'columnDisplayName' => $column->display_name,
                    'columnSrid' => $srid                    
                ]);
            }
        }
     
        return $return;   
    }
}
