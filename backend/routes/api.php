<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



$data =
[
    'prefix' => '',  
    'namespace' => '', 
    'middleware' => ['api']
];

Route::any('/', '\App\Http\Controllers\GeneralController@service_ok');//Tested
    


/*******    V1    *******/   

$data['prefix'] = 'v1';   
$data['namespace'] = 'Api\V1';
Route::group($data, function () { require 'api_standart.php'; });