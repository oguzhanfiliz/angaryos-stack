<?php
use App\BaseModel;

$report_types = [];

$report_types['record'] = 'Bilgi kartı raporu';
$report_types['table'] = 'Veri tablosu raporu';

$temp = $this->get_base_record();

foreach($report_types as $name => $display_name)
{
    $temp['name'] = $name;
    $temp['display_name'] = $display_name;
    
    $report_types[$name] = new BaseModel('report_types', $temp);
    $report_types[$name]->save();
}