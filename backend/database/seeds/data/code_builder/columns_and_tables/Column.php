<?php

use App\BaseModel;

$column_gui_type_override =
[
    'php_code' => $column_gui_types['codeeditor:php']->id,
    'sql_code' => $column_gui_types['codeeditor:sql']->id,
    'style_code' => $column_gui_types['codeeditor:html']->id,
    'column_array_ids' => $column_gui_types['multiselectdragdrop']->id,
    //'column_group_ids' => $column_gui_types['multiselectdragdrop']->id,
    'column_ids' => $column_gui_types['multiselectdragdrop']->id,
    'profile_picture' => $column_gui_types['files']->id,
    'image' => $column_gui_types['files']->id,
    'password' => $column_gui_types['password']->id
];

$columns[$column->name] = 
[
    'name' => $column->name,
    'display_name' => $column_name_display_name_map[$column->name],
    'column_db_type_id' => $column_db_types[$colmn_db_type_map[$column->type]]->id,
    'column_gui_type_id' => $column_gui_types[$colmn_gui_type_map[$column->type]]->id,
    'column_table_relation_id' => NULL,
    'subscriber_ids' => NULL,
    'column_validation_ids' => $column_type_validation_map[$column->type],
    'default' => NULL
];

if(isset($column_collective_info_list[$column->name]))
{
    $type = $column_collective_info_list[$column->name];
    $columns[$column->name]['column_collective_info_id'] = $column_collective_infos[$type]->id;
}

if(isset($column_gui_triggers[$column->name]))
{
    $ids = [];
    foreach($column_gui_triggers[$column->name] as $trigger)
        array_push ($ids, $trigger->id);
    
    $columns[$column->name]['column_gui_trigger_ids'] = $ids;
}

if(isset($column_table_relations[$column->name])) 
    $columns[$column->name]['column_table_relation_id'] = $column_table_relations[$column->name]->id;

if(isset($columns_validations[$column->name]))
    $columns[$column->name]['column_validation_ids'] = $columns_validations[$column->name];

if(isset($up_columns[$column->name]))
    $columns[$column->name]['up_column_id'] = $up_columns[$column->name]->id;

if(isset($subscribers['column'][$column->name]))
{
    $columns[$column->name]['subscriber_ids'] = [];
    foreach($subscribers['column'][$column->name] as $sub)
        array_push ($columns[$column->name]['subscriber_ids'], $sub->id);
}

if(isset($column_gui_type_override[$column->name]))
    $columns[$column->name]['column_gui_type_id'] = $column_gui_type_override[$column->name];
else if(strstr($column->name, '_id'))
{
    if(strstr($column->name, '_ids'))
        $columns[$column->name]['column_gui_type_id'] = $column_gui_types['multiselect']->id;
    else
        $columns[$column->name]['column_gui_type_id'] = $column_gui_types['select']->id;
}

$temp = $this->get_base_record();
$temp = array_merge($columns[$column->name], $temp);

$columns[$column->name] = new BaseModel('columns', $temp);
$columns[$column->name]->save();

echo "\tColumn OK: " . $column->name . "\n\n";