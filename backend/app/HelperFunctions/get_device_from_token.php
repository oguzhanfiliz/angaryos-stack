<?php

use \App\BaseModel;

$temp = explode('t', $params);
$temp = last($temp);
$temp = explode('d', $temp);
    
if(count($temp) != 2) return NULL;
if((int)$temp[0] < 1) return NULL;
if((int)$temp[1] < 1) return NULL;

$tableName = get_attr_from_cache('tables', 'id', $temp[0], 'name');

$device = new \App\BaseModel($tableName);
$device = $device->find($temp[1]);

if(!$device) return NULL;

$device = helper('clear_device_token', $device);

if(!helper('control_token', ['record' => $device, 'token' => $params])) return NULL;

return $device;