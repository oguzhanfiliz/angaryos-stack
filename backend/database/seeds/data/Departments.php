<?php
use App\BaseModel;


$departments = ['Bilgi İşlem Müdürlüğü'];

$temp = $this->get_base_record();

foreach($departments as $name)
{
    $temp['name_basic'] = $name;
    
    $departments[$name] = new BaseModel('departments', $temp);
    $departments[$name]->save();
}