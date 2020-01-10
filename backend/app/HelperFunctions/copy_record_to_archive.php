<?php

if($tableName == NULL)
{
    $tableName = $record->getTable();
    $record = $record->toArray();
}

$record = (array)$record;

$record['record_id'] = $record['id'];
unset($record['id']);

$record = new \App\BaseModel($tableName.'_archive', $record);

$record->created_at = $record['updated_at'];
$record->own_id = $record['user_id'];
$record->updated_at = \Carbon\Carbon::now();
$record->user_id = \Auth::user()->id;

$record->save();

return TRUE;