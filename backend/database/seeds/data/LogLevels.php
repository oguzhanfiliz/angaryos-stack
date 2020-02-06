<?php
use App\BaseModel;


$levels = 
[
    'debug' => [],
    'info' => [],
    'warning' => [], 
    'danger' => []
];

$temp = $this->get_base_record();

foreach($levels as $name => $level)
{
    $levels[$name] = new BaseModel('log_levels', array_merge($temp, ['name' => $name]));
    $levels[$name]->save();
}