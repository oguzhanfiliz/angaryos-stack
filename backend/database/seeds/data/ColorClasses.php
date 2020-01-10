<?php
use App\BaseModel;


$color_classes = [];

$color_classes['none'] = 'Yok';
$color_classes['primary'] = 'Birincil';
$color_classes['secondary'] = 'Boş';
$color_classes['danger'] = 'Tehlike';
$color_classes['warning'] = 'Uyarı';
$color_classes['info'] = 'Bilgi';

$temp = $this->get_base_record();

foreach($color_classes as $name => $display_name)
{
    $temp['name'] = $name;
    $temp['display_name'] = $display_name;
    
    $color_classes[$name] = new BaseModel('color_classes', $temp);
    $color_classes[$name]->save();
}