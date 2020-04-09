<?php

try { $record->fillVariables(); }
catch(\Exception $e) {}
catch(\Error $e) {}

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

$user = \Auth::user();
if($user == NULL) $user = \App\User::find(ROBOT_USER_ID);

$record->user_id = $user->id;

$geometryColumnTypes = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];

$tempData = [];
foreach($record->getAllColumnsFromDB() as $columnName => $array)
{
    if(!in_array($array['type'], $geometryColumnTypes)) continue;
    
    $temp = substr($record->{$columnName}, 0, 1);
    if($temp != '1' && $temp != '0') continue;
    
    $tempData[$columnName] = $record->{$columnName};
    $record->{$columnName} = NULL;
}

$record->save();

if($tempData == []) return TRUE;

DB::table($tableName.'_archive')->where('id', $record->id)->update($tempData);
return TRUE;