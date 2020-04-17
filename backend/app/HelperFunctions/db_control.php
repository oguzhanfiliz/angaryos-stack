<?php

if(defined('ROBOT_USER_ID')) return TRUE;
if(\Request::segment(3) == 'initializeDb') return TRUE;
if(strstr(@$_SERVER['argv'][0], 'artisan')) return TRUE;
    
$data = 
[
    'status' => 'error',
    'code' => 400,
    'data' => ['message' => 'db.is.not.initialized']
];            

abort(response()->json($data, $data['code']));