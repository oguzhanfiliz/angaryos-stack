<?php

namespace App\Libraries;

use \App\Libraries\GeoServerLibrary;

use DB;

class TableGeoServerOperationsLibrary 
{
    private $geoColumns = ['point', 'multipoint', 'linestring', 'multilinestring', 'polygon', 'multipolygon'];
    
    
    
    /****  Main Subscriber Functions  ****/
    
    public function TableEvent($params)
    {
        return $this->{'TableEventFor'.ucfirst($params['type'])}($params);
    }
    
    
    
    /****    Events    ****/

    public function TableEventForUpdate($params)
    {
        return $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForCreate($params)
    {
        return $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForClone($params)
    {
        return $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForRestore($params)
    {
        return $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForDelete($params) 
    {
        return NULL; 
    }
    
    
    
    /****    Common Functions    ****/
    
    public function CreateLayerIfNotExist($params)
    {
        if(!$this->TableIsHasGeoColumn($params['table'])) return;

        $helper = $this->GetGeoServerHelper();
        
        $this->WorkspaceControl($helper);
        $this->DataStoreControl($helper);
        
        if($this->LayerIsExistOnGeoServer($helper, $params)) return;
        
        $this->CreateViewIfNotExistForLayer($params['table']);
        
        $temp = $helper->createLayer('v_'.$params['table']->name, $helper->workspaceName, $helper->dataStoreName);
        if($temp != '') \Log::error('Geoserver layer oluşturulamadı! (Hata: ' . $temp . ')');        
    }
    
    private function WorkspaceIsExist($helper)
    {
        $workspaces = $helper->listWorkspaces();
        if(gettype($workspaces->workspaces) != 'object') return FALSE;
        
        foreach($workspaces->workspaces->workspace as $workspace)
            if($workspace->name == $helper->workspaceName)
                return TRUE;
            
        return FALSE;
    }
    
    private function WorkspaceControl($helper)
    {
        if($this->WorkspaceIsExist($helper)) return;
        
        $helper->createWorkspace($helper->workspaceName);
    }
    
    private function DataStoreIsExist($helper)
    {
        $dataStores = $helper->listDatastores($helper->workspaceName);
        if(gettype($dataStores->dataStores) != 'object') return FALSE;
        
        foreach($dataStores->dataStores->dataStore as $dataStore)
            if($dataStore->name == $helper->dataStoreName)
                return TRUE;
            
        return FALSE;
    }
    
    private function DataStoreControl($helper)
    {
        if($this->DataStoreIsExist($helper)) return;
        
        $helper->createPostGISDataStore(
                $helper->dataStoreName, 
                $helper->workspaceName, 
                env('DB_DATABASE', 'postgres'), 
                env('DB_USERNAME', 'postgres'), 
                env('DB_PASSWORD', '1234Aa.'), 
                env('DB_HOST', 'postgresql'), 
                env('DB_PORT', '5432'));
    }
    
    private function TableIsHasGeoColumn($table)
    {
        $columns = $table->getRelationData('column_ids');

        foreach($columns as $column)
        {
            $type = $column->getRelationData('column_db_type_id');
            if(in_array($type->name, $this->geoColumns))
                return TRUE;
        }
        
        return FALSE;
    }
    
    private function GetGeoServerHelper()
    {
        $helper = new GeoServerLibrary(
                                        env('GEOSERVER_URL', 'http://geoserver:8080/geoserver/'),
                                        env('GEOSERVER_USER', 'admin'),
                                        env('GEOSERVER_PASSWORD', 'geoserver'));

        $helper->workspaceName = env('GEOSERVER_WORKSPACE', 'angaryos');
        $helper->dataStoreName = env('GEOSERVER_DATA_STORE', 'angaryos');
        
        return $helper;
    }
    
    private function LayerIsExistOnGeoServer($helper, $params)
    {
        $layers = $helper->listLayers($helper->workspaceName, $helper->dataStoreName);
        if($layers == NULL) dd('Geoserver unavilable');

        if($layers->featureTypes != '')
            foreach($layers->featureTypes->featureType as $f)
                if($f->name == 'v_'.$params['table']->name)
                    return TRUE;
                
        return FALSE;
    }
    
    private function CreateViewIfNotExistForLayer($table)
    {
        $viewName = 'v_'.$table->name;
        
        $viewNames = \DB::select('select table_name as name from INFORMATION_SCHEMA.views');
        
        $control = FALSE;
        foreach ($viewNames as $temp)
            if($temp->name == $viewName)
            {
                $control = TRUE;
                break;
            }

        if(!$control)
            DB::select('create view '.$viewName.' as select * from '.$table->name);
    }
}