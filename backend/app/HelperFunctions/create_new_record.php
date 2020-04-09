<?php

if($user == NULL) $user = \Auth::user();
if($user == NULL) $user = \App\User::find(ROBOT_USER_ID);

$record = new App\BaseModel($tableName);

$columns = $record->getAllColumnsFromDB();

$keys = array_keys($columns);
if(!in_array('column_set_id', $keys)) unset($data->column_set_id);

$helper = new App\Libraries\ChangeDataLibrary();
$record = $helper->updateData($columns, $data, $record);            

$record->user_id = $user->id;
$record->own_id = $user->id;

$record->save();

return $record;