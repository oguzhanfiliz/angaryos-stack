<?php
use App\BaseModel;

$now = \Carbon\Carbon::now();

$data_source_rmt_table_id_join1 = new BaseModel('join_tables');
$data_source_rmt_table_id_join1->name_basic = 'Veri Kaynağı Uzak Kolon ve Veri Kaynağı Uzak Tablo bağlantısı';
$data_source_rmt_table_id_join1->join_table_id = $tables['data_source_remote_tables']->id;
$data_source_rmt_table_id_join1->join_table_alias = 'data_source_remote_tables';
$data_source_rmt_table_id_join1->connection_column_with_alias = 'data_source_rmt_table_id';
//$data_source_rmt_table_id_join1->join_connection_type = '=';
$data_source_rmt_table_id_join1->join_column_id = $columns['id']->id;
$data_source_rmt_table_id_join1->state = TRUE;
$data_source_rmt_table_id_join1->updated_at = $now;
$data_source_rmt_table_id_join1->created_at = $now;
$data_source_rmt_table_id_join1->user_id = ROBOT_USER_ID;
$data_source_rmt_table_id_join1->own_id = ROBOT_USER_ID;
$data_source_rmt_table_id_join1->save();

$data_source_rmt_table_id_join2 = new BaseModel('join_tables');
$data_source_rmt_table_id_join2->name_basic = 'Veri Kaynağı Uzak Tablo ve Veri Kaynağı bağlantısı';
$data_source_rmt_table_id_join2->join_table_id = $tables['data_sources']->id;
$data_source_rmt_table_id_join2->join_table_alias = 'data_sources';
$data_source_rmt_table_id_join2->connection_column_with_alias = 'data_source_remote_tables.data_source_id';//'data_source_remote_tables.data_source_id';
//$data_source_rmt_table_id_join2->join_connection_type = '=';
$data_source_rmt_table_id_join2->join_column_id = $columns['id']->id;
$data_source_rmt_table_id_join2->state = TRUE;
$data_source_rmt_table_id_join2->updated_at = $now;
$data_source_rmt_table_id_join2->created_at = $now;
$data_source_rmt_table_id_join2->user_id = ROBOT_USER_ID;
$data_source_rmt_table_id_join2->own_id = ROBOT_USER_ID;
$data_source_rmt_table_id_join2->save();

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
    /*$column_group_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['column_groups']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],  */  
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
    $data_source_rmt_table_id_relation ['relation_sql'] =>
    [
        'relation_table_id' => $tables['data_source_remote_tables']->id,
        'relation_source_column_id' => NULL,
        'relation_display_column_id' => NULL,
        'relation_sql' => NULL,
        'join_table_ids' => [$data_source_rmt_table_id_join1->id, $data_source_rmt_table_id_join2->id],
        'relation_source_column' => 'id',
        'relation_display_column' => 'data_sources.name || \' - \' ||data_source_remote_tables.name_basic'
    ],
    $data_source_remote_column_id_relation ['relation_sql'] =>
    [
        'relation_table_id' => $tables['data_source_remote_columns']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name_basic']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $data_source_col_relation_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['data_source_col_relations']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['id']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $data_source_table_relation_ids_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['data_source_tbl_relations']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['id']->id,
        'relation_sql' => NULL,
        'relation_source_column' => NULL,
        'relation_display_column' => NULL
    ],
    $log_level_id_relation['relation_sql'] =>
    [
        'relation_table_id' => $tables['log_levels']->id,
        'relation_source_column_id' => $columns['id']->id,
        'relation_display_column_id' => $columns['name']->id,
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