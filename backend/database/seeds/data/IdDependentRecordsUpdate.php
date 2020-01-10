<?php
use App\BaseModel;

$update_data =
[
    $table_id_relation['relation_sql'] => 
    [
        'relation_table_id' => $tables['tables']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_id_relation['relation_sql'] => 
    [
        'relation_table_id' => $tables['columns']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_validation_ids_relation['relation_sql'] => 
    [
        'relation_table_id' => $tables['column_validations']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['validation_with_params']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_set_type_id_relation['relation_sql'] => 
    [
        'relation_table_id' => $tables['column_set_types']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_array_type_id_relation['relation_sql'] => 
    [
        'relation_table_id' => $tables['column_array_types']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_collective_info_id_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_collective_infos']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_gui_trigger_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_gui_triggers']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_table_relation_id_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_table_relations']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $subscriber_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['subscribers']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $join_table_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['join_tables']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],    
    $column_array_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_arrays']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],    
    $column_group_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_groups']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],    
    $department_id_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['departments']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],  
    $up_column_id_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['up_columns']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ], 
    $column_db_type_id_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_db_types']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $column_gui_type_id_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_gui_types']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['display_name']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
];

foreach($column_table_relations as $relation)
{
    if(!isset($relation->relation_sql)) 
        continue;
    if(!isset($update_data[$relation->relation_sql]))
        continue;
    
    $temp = $this->get_base_record();
    $temp = array_merge($temp, $update_data[$relation->relation_sql]);
    
    $record = new BaseModel('column_table_relations');
    $record = $record->where('id', $relation->id) ->update($temp);
}