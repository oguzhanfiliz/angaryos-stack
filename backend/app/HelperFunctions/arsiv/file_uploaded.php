<?php

$column = $params['columnName'];

$old = @\Request::input($column.'_old');

if($old == NULL) $old = [];
else $old = json_decode($old, TRUE);

$oldReturn = [ $column => json_encode($old) ];

if($params['type'] == 'delete') return $oldReturn;
if($params['type'] == 'clone') return $oldReturn;
if($params['type'] == 'restore') return $oldReturn;

if(!isset($_FILES[$column])) return $oldReturn;

$helper = new \App\Libraries\FileLibrary();
$files = $helper->fileUploaded($column);

$return = [];
foreach($files as $file)
{
    $temp['destination_path'] = $file['destinationPath'];
    $temp['file_name'] = $file['fileName'];
    $temp['disk'] = $file['disk'];
    
    array_push($return, $temp);
}

$return = array_merge($old, $return);

return [ $column => json_encode($return) ];