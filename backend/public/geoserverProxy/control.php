<?php

if(!isset($_SERVER['REQUEST_URI'])) return;

$temp = explode('?', $_SERVER['REQUEST_URI']);
if(count($temp) != 2) return;

$segments = explode('/', $temp[0]);
if($segments[4] != 'getMapTile') return;

$requests = [];
foreach(explode('&', $temp[1]) as $req)
{
    if(!strstr($req, '=')) continue;
    
    $set = explode('=', $req);
    $requests[strtoupper($set[0])] = $set[1];
}

if(!strstr($requests['LAYERS'], '%3Av_')) return;
$temp = explode('%3Av_', $requests['LAYERS']);

return ['segments' => $segments, 'requests' =>  $requests, 'tableName' => $temp[1]];

?>