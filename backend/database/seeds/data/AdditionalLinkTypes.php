<?php
use App\BaseModel;

$additional_link_types = [];
$additional_link_types['standart'] = NULL;

$temp = $this->get_base_record();

foreach($additional_link_types as $additional_link_type => $null)
{
    $temp['name'] = $additional_link_type;
    
    $column_validations[$validation] = new BaseModel('additional_link_types', $temp);
    $column_validations[$validation]->save();
}