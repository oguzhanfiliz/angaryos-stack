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
    
    public function StyleEvent($params)
    {
        $code = @$params['requests']['style_code'];
        if(!$code)  @$code = $params['record']->style_code;
        
        if(strstr($code, '<?xml '))
            return $this->{'StyleEventFor'.ucfirst($params['type'])}($params);
    }
    
    public function CustomLayerEvent($params)
    {
        return $this->{'CustomLayerEventFor'.ucfirst($params['type'])}($params);
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
    
    public function TableEventForImport($params)
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
    
    public function StyleEventForCreate($params)
    {
        $helper = $this->GetGeoServerHelper();
        
        $SLD = $params['requests']['style_code'];
        $styleName = $params['requests']['name'];
        $styleName = helper('seo', $styleName);
        
        $r = $helper->createStyle($styleName, $SLD);
        if(!$r) custom_abort('style.not.created.on.geoserver');
    }
    
    public function StyleEventForUpdate($params)
    {
        $oldStyleName = $params['record']->name;
        $newStyleName = $params['requests']['name'];

        if(isset($params['requests']['style_code']))
            $SLD = $params['requests']['style_code'];
        else
            $SLD = $params['record']->style_code;
        
        if($oldStyleName != $newStyleName)
            custom_abort('style.name.not.changable');
        
        $helper = $this->GetGeoServerHelper();
        
        $r = $helper->updateStyle($oldStyleName, $SLD);
        if(!$r) custom_abort('style.not.updated.on.geoserver');
    }
    
    public function StyleEventForRestore($params)
    {
        $old = get_attr_from_cache('layer_styles', 'id', $params['record']['record_id'], '*');
        
        $oldStyleName = $old->name;
        $newStyleName = $params['record']->name;
        $SLD = $params['record']->style_code;
        
        if($oldStyleName != $newStyleName)
            custom_abort('style.name.not.changable');
        
        $helper = $this->GetGeoServerHelper();
        
        $r = $helper->updateStyle($oldStyleName, $SLD);
        if(!$r) custom_abort('style.not.updated.on.geoserver');
    }
    
    public function StyleEventForDelete($params)
    {
        $helper = $this->GetGeoServerHelper();
        
        $styleName = $params['record']->name;
        $styleName = helper('seo', $styleName);
        
        $r = $helper->deleteStyle($styleName);
        if(strlen($r) != 0) custom_abort('style.not.deleted.on.geoserver');
    }
    
    public function CustomLayerEventForCreate($params)
    {
        if(!isset($params['requests']['table_id']) || strlen($params['requests']['table_id']) == 0)
            $params['requests']['table_id'] = $params['record']->table_id;
        
        $table = get_model_from_cache('tables', 'id', $params['requests']['table_id']);
        if(!$this->TableIsHasGeoColumn($table)) 
            custom_abort('table.not.has.geo.column');
        
        $helper = $this->GetGeoServerHelper();
        
        $this->WorkspaceControl($helper);
        $this->DataStoreControl($helper);
        
        $customLayerName = helper('seo', $params['requests']['name']);
        $temp = $helper->createLayer(
                                        'v_'.$table->name, 
                                        $customLayerName, 
                                        $helper->workspaceName, 
                                        $helper->dataStoreName);
        
        if($temp != '') 
        {
            \Log::error('Geoserver layer oluşturulamadı! (Hata: ' . $temp . ')');
            custom_abort('layer.not.created.on.geoserver:'.$temp);
        }
        
        
        if(!isset($params['requests']['layer_style_id']) || strlen($params['requests']['layer_style_id']) == 0)
            $params['requests']['layer_style_id'] = $params['record']->layer_style_id;
        
        $styleName = get_attr_from_cache('layer_styles', 'id', $params['requests']['layer_style_id'], 'name');
        $styleName = helper('seo', $styleName);
        
        
        if(!isset($params['requests']['custom_layer_type_id']) || strlen($params['requests']['custom_layer_type_id']) == 0)
            $params['requests']['custom_layer_type_id'] = $params['record']->custom_layer_type_id;
        
        $type = get_attr_from_cache('custom_layer_types', 'id', $params['requests']['custom_layer_type_id'], 'name');
        if($type == 'wfs') return;
        
        $r = $helper->addStyleToLayer($customLayerName, $styleName);
        if($styleName != $r)
            \Log::error('Geoserver layer oluşturuldu ama sitil atanamadı! (Hata: ' . $r . ')');
    }
    
    public function CustomLayerEventForUpdate($params)
    {
        if(!isset($params['requests']['name']) || strlen($params['requests']['name']) == 0) return;
        
        $oldLayerName = helper('seo', $params['record']->name);
        $newLayerName = helper('seo', $params['requests']['name']);
        
        $helper = $this->GetGeoServerHelper();
        
        $r = $helper->deleteLayer($oldLayerName, $helper->workspaceName, $helper->dataStoreName);
        if(strlen($r) != 0)
            \Log::error('Geoserver layer silinemedi! (Hata: ' . $r . ')');
        
        $this->CustomLayerEventForCreate($params);
    }
    
    public function CustomLayerEventForDelete($params)
    {
        $oldLayerName = helper('seo', $params['record']->name);
        
        $helper = $this->GetGeoServerHelper();
        
        $r = $helper->deleteLayer($oldLayerName, $helper->workspaceName, $helper->dataStoreName);
        if(strlen($r) != 0)
            custom_abort ('layer.not.deleted.on.geoserver');
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
        
        $name = 'v_'.$params['table']->name;
        $temp = $helper->createLayer($name, $name, $helper->workspaceName, $helper->dataStoreName);
        if($temp != '') 
        {
            \Log::error('Geoserver layer oluşturulamadı! (Hata: ' . $temp . ')');
            custom_abort('layer.not.created.on.geoserver:'.$temp);
        }
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