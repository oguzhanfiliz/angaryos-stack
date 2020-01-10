<?php
use App\BaseModel;

$column_array_types = [];

$column_array_types['direct_data'] = 'Datanın Kendisi';
$column_array_types['table_from_data'] = 'Dataya Bağlı Tablo';

$temp = $this->get_base_record();

foreach($column_array_types as $name => $display_name)
{
    $temp['name'] = $name;
    $temp['display_name'] = $display_name;
    
    $column_array_types[$name] = new BaseModel('column_array_types', $temp);
    $column_array_types[$name]->save();
}