<?php
use App\BaseModel;

$data_source_types = ['postgresql', 'ldap'];

foreach($data_source_types as $name)
{
    $type = [];
    $type['name'] = $name;
    
    $temp = $this->get_base_record();
    $temp = array_merge($temp, $type);
    
    $data_source_types[$name] = new BaseModel('data_source_types', $temp);
    $data_source_types[$name]->save();
}

$data_source_directions = ['toDataSource', 'fromDataSource', 'twoWay'];

foreach($data_source_directions as $name)
{
    $type = [];
    $type['name'] = $name;
    
    $temp = $this->get_base_record();
    $temp = array_merge($temp, $type);
    
    $data_source_directions[$name] = new BaseModel('data_source_directions', $temp);
    $data_source_directions[$name]->save();
}
