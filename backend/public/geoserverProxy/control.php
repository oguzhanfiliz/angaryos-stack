<?php

if(!isset($_SERVER['REQUEST_URI'])) return;

$temp = explode('?', $_SERVER['REQUEST_URI']);
if(count($temp) != 2) return;

$segments = explode('/', $temp[0]);
if(@$segments[4] != 'getMapData') return;

$requests = [];
foreach(explode('&', $temp[1]) as $req)
{
    if(!strstr($req, '=')) continue;
    
    $set = explode('=', $req);
    $requests[strtoupper($set[0])] = $set[1];
}

$tableName = '';

$type = strtolower(@$requests['SERVICE']);
if($type == 'wms')
{
    if(isset($requests['LAYERS'])) $key = 'LAYERS';
    else if(isset($requests['LAYER'])) $key = 'LAYER';
    else bb('layer(s).param.not.found');

    $temp = explode('%3A', $requests[$key]);
    $tableName = $temp[1];
    
    if(substr($tableName, 0, 2) == 'v_')
        $tableName = substr($tableName, 2);
}
else if($type == 'wfs')
{
    $temp = explode(':', $requests['TYPENAME']);
    $tableName = $temp[1];
}
else bb('undefined.service.type:'.$type);

return ['segments' => $segments, 'requests' =>  $requests, 'tableName' => $tableName];

?>