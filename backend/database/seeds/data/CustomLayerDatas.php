<?php
use App\BaseModel;

$custom_layer_types = ['wms', 'wfs'];

foreach($custom_layer_types as $name)
{
    $type = [];
    $type['name'] = $name;
    
    $temp = $this->get_base_record();
    $temp = array_merge($temp, $type);
    
    $custom_layer_types[$name] = new BaseModel('custom_layer_types', $temp);
    $custom_layer_types[$name]->save();
}