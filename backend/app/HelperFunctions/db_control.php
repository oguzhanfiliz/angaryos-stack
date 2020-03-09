<?php

if(
    \Request::segment(3) == 'initializeDb' 
    ||
    strstr(@$_SERVER['argv'][0], 'artisan')
) 
    return TRUE;
    
if(defined('ROBOT_USER_ID')) return TRUE;

if($params == TRUE) return FALSE;
    
$data = 
[
    'status' => 'error',
    'code' => 400,
    'data' => ['message' => 'db.is.not.initialized']
];            

abort(response()->json($data, $data['code']));