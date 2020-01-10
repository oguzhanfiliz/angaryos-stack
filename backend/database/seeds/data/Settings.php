<?php
use App\BaseModel;


$settings = [];

$settings['DB_SCHEMA'] = 'public';
$settings['REC_COUNT_PER_PAGE'] = 10;
$settings['DB_PROJECTION'] = 7932;
$settings['UPLOAD_PATH'] = 'uploads';
$settings['STAMP_TEXT'] = 'Angaryos';
$settings['PUBLIC_USER_ID'] = 2;
$settings['ROBOT_USER_ID'] = 3;
$settings['SHOW_DELETED_TABLES_AND_COLUMNS'] = '0';

$temp = $this->get_base_record();

foreach($settings as $name => $value)
{
    $temp['name'] = $name;
    $temp['value'] = $value;
    
    $settings[$name] = new BaseModel('settings', $temp);
    $settings[$name]->save();
}