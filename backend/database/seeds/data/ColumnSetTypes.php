<?php
use App\BaseModel;


$column_set_types = [];

$column_set_types['none'] = 'Yok';
$column_set_types['tab'] = 'Sekme';
$column_set_types['accordion'] = 'Akordiyon';
$column_set_types['group_box'] = 'Grup Kutusu';
$column_set_types['collapse'] = 'Buton Altına Gizli';
$column_set_types['seteps'] = 'Aşamalı';

$temp = $this->get_base_record();

foreach($column_set_types as $name => $display_name)
{
    $temp['name'] = $name;
    $temp['display_name'] = $display_name;
    
    $column_set_types[$name] = new BaseModel('column_set_types', $temp);
    $column_set_types[$name]->save();
}