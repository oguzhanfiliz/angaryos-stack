<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearGeoserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() { }
    
    private function WaitForService()
    {
        $now = \Carbon\Carbon::now();
        $state = FALSE;
        
        while(TRUE)
        {
            $url = env('GEOSERVER_URL', 'http://geoserver:8080/geoserver/');
            $html = @file_get_contents($url);
            
            if($html)
            {
                $state = TRUE;
                break;
            }
            
            sleep(5);
            $waitTime = $now->diffInSeconds(\Carbon\Carbon::now());
            if($waitTime > (60*10)) break;
            else echo 'wait.'.$waitTime.'<br>'."\n";
        }
        
        if(!$state)
            throw new \Exception('Geoserver unavilable for 10 minute');
    }
    
    private function ScaleService($scale)
    {
        $r = exec('echo www | sudo -S docker service scale angaryos_geoserver='.$scale);
        $r = ($r == 'verify: Service converged');
                
        if(!$r) \Log::alert('Geoserver scale error! scale: '.$scale);   
        else \Log::alert('Geoserver scale success. scale: '.$scale); 
                
        return $r;
    }
    
    private function RemoveAllFiles()
    {   
        $base = '/var/geoserver/';
        $files = ['.', '..', '.gitignore', 'logs', 'user_projections'];
        foreach(scandir($base) as $fileOrDir)
            if(!in_array($fileOrDir, $files))
                exec('echo www | sudo -S rm -rf '.$base.$fileOrDir);
            
        \Log::alert('Geoserver remove files ok');
    }
    
    private function GetGeoserverLibrary()
    {
        $username = env('GEOSERVER_USER', 'admin');
        $password = env('GEOSERVER_PASSWORD', 'geoserver');
        $workspace = env('GEOSERVER_WORKSPACE', 'angaryos');
        
        $helper = new \App\Libraries\GeoServerLibrary(
                                                env('GEOSERVER_URL', 'http://geoserver:8080/geoserver/'),
                                                $username,
                                                $password);
        
        $helper->workspaceName = $workspace;
        $helper->dataStoreName = env('GEOSERVER_DATA_STORE', 'angaryos');
        
        \Log::alert('Create geoserver library ok');
        
        return $helper;
    }
    
    private function SetDefault()
    {
        $username = env('GEOSERVER_USER', 'admin');
        $password = env('GEOSERVER_PASSWORD', 'geoserver');
        $workspace = env('GEOSERVER_WORKSPACE', 'angaryos');
        
        $exec = 'echo www | sudo -S python3 /var/www/geoserverbot.py';
        $exec .= ' ' . $username;
        $exec .= ' ' . $password;
        $exec .= ' ' . $workspace;
        
        $r = exec($exec);
        if($r != 'LOG -> level:1 - Browser closed')
            throw new \Exception('Geoserver set default error: ' . json_encode($r));
        
        \Log::alert('Geoserver set default ok');
    }
    
    private function CreateWorkspace($helper)
    {
        $workspaces = $helper->listWorkspaces();
        if($workspaces == NULL)
            throw new \Exception('Geoserver unavilable for create workspace');
        
        $control = TRUE;
        if($workspaces->workspaces != '')
            foreach($workspaces->workspaces->workspace as $w)
                if($w->name == $helper->workspaceName)
                {
                    $control = FALSE;
                    break;
                }

        if($control)
            $helper->createWorkspace($helper->workspaceName);
        
        \Log::alert('Geoserver create workspace ok');
    }
    
    private function CreateDataStore($helper)
    {        
        $dataStores = $helper->listDatastores($helper->workspaceName);
        if($dataStores == NULL) 
            throw new \Exception('Geoserver unavilable for create datastore');
        
        $control = TRUE;
        if($dataStores->dataStores != '')
            foreach($dataStores->dataStores->dataStore as $d)
                if($d->name == $helper->dataStoreName)
                {
                    $control = FALSE;
                    break;
                }
                
        if($control)
            $helper->createPostGISDataStore(
                    $helper->dataStoreName, 
                    $helper->workspaceName, 
                    env('DB_DATABASE', 'postgres'), 
                    env('DB_USERNAME', 'postgres'), 
                    env('DB_PASSWORD', '1234Aa.'), 
                    env('DB_HOST', '1234Aa.'));
        
        \Log::alert('Geoserver create datastore ok');
    }
    
    private function createUsersLayer()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'users', 'id');
        LayerOperationOnGeoserver::dispatch('create', $tableId);
        
        \Log::alert('Geoserver create users layer job ok');
    }

    public function handle()
    {
        $this->WaitForService();
        
        $this->ScaleService(0);
        $this->RemoveAllFiles();
        $this->ScaleService(1);
        
        $this->WaitForService();
        
        $helper = $this->GetGeoserverLibrary();
        
        $this->CreateWorkspace($helper);
        $this->CreateDataStore($helper);
        $this->SetDefault();     
                
        $this->createUsersLayer();
    }
}