<?php
use App\BaseModel;

$additional_link_types = [];
$additional_link_types['standart'] = NULL;

$temp = $this->get_base_record();

foreach($additional_link_types as $additional_link_type => $null)
{
    $temp['name'] = $additional_link_type;
    
    $additional_link_types[$additional_link_type] = new BaseModel('additional_link_types', $temp);
    $additional_link_types[$additional_link_type]->save();
}