<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;

class GeneralController extends Controller
{
    public function __construct()
    {
        //\Cache::flush();
    }
    
    public function initializeDB()
    {
        $output = helper('initialize_db');
        if($output === TRUE)
            abort(helper('response_success', 'db.initialize.ok'));
        else if($output === FALSE)
            abort(helper('response_error', 'db.already.initialized'));
        else
            abort(helper('response_error', 'db.not.initialized: '.$output));
    }
    
    public function serviceOk($user = NULL) 
    {
        return helper('response_service_ok');         
    }
}
