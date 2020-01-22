<?php 
use App\BaseModel;

$temp = $this->get_base_record();
$temp['name_basic'] = $column_name_display_name_map[$column->name] . ' kolonu için '.$up_columns[$column->name]['name_basic'];

if(is_numeric($up_columns[$column->name]['column_id']))
    $temp['column_id'] = $up_columns[$column->name]['column_id'];
else
    $temp['column_id'] = $columns[$up_columns[$column->name]['column_id']]->id;


if(is_numeric($up_columns[$column->name]['source_column_id']))
    $temp['source_column_id'] = $up_columns[$column->name]['source_column_id'];
else
    $temp['source_column_id'] = $columns[$up_columns[$column->name]['source_column_id']]->id;


$temp['table_ids'] = [];

foreach($up_columns[$column->name]['table_ids'] as $tableName)
    if(is_numeric($tableName))
        array_push($temp['table_ids'], $tableName);
    else
        array_push($temp['table_ids'], $tables[$tableName]->id);

$up_columns[$column->name] = new BaseModel('up_columns', $temp);
$up_columns[$column->name]->save();


echo "\t\tUp Column OK: " . $column->name . "\n";