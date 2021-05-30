<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;

class GeneralController extends Controller
{
    use GeneralControllerTrait;
    
    public function __construct()
    {
        //\Cache::flush();
    }
    
    public function test($user)
    {
        return 'test';
    }
    
    public function logs($user)
    {
        $debugUserIds = json_decode(DEBUG_USER_IDS);
        if(!in_array($user->id, $debugUserIds)) custom_abort('no.auth');
            
        $ctrl = new \Rap2hpoutre\LaravelLogViewer\LogViewerController();
        return $ctrl->index();
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
    
    public function upgradeDb()
    {
        $upgradeNumber = @fopen("/var/www/.upgradenumber", "r");
        if(!$upgradeNumber) $upgradeNumber = 0;
        
        $current = @file_get_contents('https://raw.githubusercontent.com/MikroGovernment/angaryos-stack/master/backend/.upgradenumber?'.rand());
        if(!$current) return helper('response_error', 'no.upgrade.number');

        for($i = 0; $i < $current; $i++)
        {
            $fn = 'upgradeDb'.$i.'to'.($i+1);
            $this->{$fn}();
        }

        return helper('response_success', 'OK');
    }

    private function upgradeDb0to1() { }
    
    public function importRecord($user)
    {
        send_log('info', 'Request Import Record');
        
        $this->UserImportRecordAuthControl($user);
        
        $files = \Request::file('files');
        $paths = $this->MoveUploadedFileToTempFolder($files);
        
        $data = $this->ImportRecordsToTables($user, $paths);
        
        send_log('info', 'Record Import Success', $data);

        return helper('response_success', $data);
    }
    
    public function serviceOk($user = NULL) 
    {
        return helper('response_service_ok');         
    }
}
