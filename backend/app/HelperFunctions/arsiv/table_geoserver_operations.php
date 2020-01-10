<?php

if($params['type'] != 'create' && $params['type'] != 'update') return;

$geoColumns = ['point', 'multipoint', 'linestring', 'multilinestring', 'polygon', 'multipolygon'];

$columns = $params['table']->getRelationData('column_ids');

$control = FALSE;
foreach($columns as $column)
{
    $type = $column->getRelationData('column_db_type_id');
    if(in_array($type->name, $geoColumns))
    {
        $control = TRUE;
        break;
    }
}

if(!$control) return;

$helper = new \App\Libraries\GeoServerLibrary(
                                                env('GEOSERVER_URL', 'http://geoserver:8080/geoserver/'),
                                                env('GEOSERVER_USER', 'admin'),
                                                env('GEOSERVER_PASSWORD', 'geoserver'));

$helper->workspaceName = env('GEOSERVER_WORKSPACE', 'angaryos');
$helper->dataStoreName = env('GEOSERVER_DATA_STORE', 'angaryos');
        
$layers = $helper->listLayers($helper->workspaceName, $helper->dataStoreName);
if($layers == NULL) dd('Geoserver unavilable');

$control = TRUE;
if($layers->featureTypes != '')
    foreach($layers->featureTypes->featureType as $f)
        if($f->name == 'v_'.$params['table']->name)
        {
            $control = FALSE;
            break;
        }

if($control)
{
    $viewNames = \DB::select('select table_name as name from INFORMATION_SCHEMA.views');
    $control = FALSE;
    foreach ($viewNames as $viewName)
        if($viewName->name == 'v_'.$params['table']->name)
            $control = TRUE;
        
    if(!$control)
        \DB::select('create view v_'.$params['table']->name.' as select * from '.$params['table']->name);
    
    $temp = $helper->createLayer('v_'.$params['table']->name, $helper->workspaceName, $helper->dataStoreName);
    //if($temp != '') dd('geoserver layer oluşturulamadı!');
}