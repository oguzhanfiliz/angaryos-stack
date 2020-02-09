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
        $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForCreate($params)
    {
        $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForClone($params)
    {
        $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForRestore($params)
    {
        $this->CreateLayerIfNotExist($params);
    }
    
    public function TableEventForDelete($params)
    {
        
    }
    
    
    
    /****    Common Functions    ****/
    
    public function CreateLayerIfNotExist($params)
    {
        if(!$this->TableIsHasGeoColumn($params['table'])) return;

        $helper = $this->GetGeoServerHelper();
        
        if(!$this->LayerIsExistOnGeoServer($helper, $params)) return;
        
        $this->CreateViewIfNotExistForLayer($params['table']);

        $temp = $helper->createLayer('v_'.$params['table']->name, $helper->workspaceName, $helper->dataStoreName);
        //if($temp != '') dd('geoserver layer oluşturulamadı!');
        
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
        foreach ($viewNames as $viewName)
            if($viewName->name == $viewName)
            {
                $control = TRUE;
                break;
            }

        if(!$control)
            DB::select('create view '.$viewName.' as select * from '.$table->name);
    }
}