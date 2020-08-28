<?php

if($params == NULL) return '';
if($params == '') return '';

$url = env('APP_URL');

switch($params->disk)
{
    case 'fileServer':
    case 'uploads':
        $url .= 'uploads/'.str_replace([' '], ['%20'], $params->destination_path.$params->file_name);
        break;
    default: dd('undefined.disk.name:'.$params->disk);
}

return $url;