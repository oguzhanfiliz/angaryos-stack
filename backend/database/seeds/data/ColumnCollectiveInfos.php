<?php
use App\BaseModel;


$column_collective_infos = [];

$column_collective_infos['sum'] = 'Toplam';
$column_collective_infos['avg'] = 'Ortalama';
$column_collective_infos['max'] = 'En çok';
$column_collective_infos['min'] = 'En az';
$column_collective_infos['count'] = 'Adet';

$temp = $this->get_base_record();

foreach($column_collective_infos as $name => $display)
{
    $temp['name'] = $name;
    $temp['display_name'] = $display;
    
    $column_collective_infos[$name] = new BaseModel('column_collective_infos', $temp);
    $column_collective_infos[$name]->save();
}


