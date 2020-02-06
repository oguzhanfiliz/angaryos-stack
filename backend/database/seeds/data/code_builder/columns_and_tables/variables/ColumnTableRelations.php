<?php

$user_id_relation =         
[
    'relation_sql' => 'select id, concat(name_basic, \' \', surname) as name_basic from users',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$table_id_relation =
[
    'relation_sql' => 'select id, display_name from tables',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

$column_id_relation =
[
    'relation_sql' => 'select id, display_name from columns',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

/*$srid_relation =
[
    'relation_sql' => 'select srid, concat(auth_name, \':\', srid) as name from spatial_ref_sys',
    'relation_source_column' => 'srid',
    'relation_display_column' => 'name',
];*/

$column_validation_ids_relation =
[
    'relation_sql' => 'select id, validation_with_params from column_validations',
    'relation_source_column' => 'id',
    'relation_display_column' => 'validation_with_params',
];

$column_set_type_id_relation = 
[
    'relation_sql' => 'select id, display_name from column_set_types',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

$column_array_type_id_relation = 
[
    'relation_sql' => 'select id, display_name from column_array_types',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

$column_collective_info_id_relation = 
[
    'relation_sql' => 'select id, display_name from column_collective_infos',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

$column_gui_trigger_ids_relation = 
[
    'relation_sql' => 'select id, display_name from column_gui_triggers',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

$column_table_relation_id_relation = 
[
    'relation_sql' => 'select id, name_basic from column_table_relations',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$subscriber_ids_relation = 
[
    'relation_sql' => 'select id, name_basic from subscribers',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$join_table_ids_relation = 
[
    'relation_sql' => 'select id, name_basic from join_tables',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$column_array_ids_relation = 
[
    'relation_sql' => 'select id, name_basic from column_arrays',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

/*$column_group_ids_relation = 
[
    'relation_sql' => 'select id, name_basic from column_groups',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];*/

$department_id_relation = 
[
    'relation_sql' => 'select id, name_basic from departments',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$up_column_id_relation =
[
    'relation_sql' => 'select id, name_basic from up_columns',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$column_db_type_id_relation = 
[
    'relation_sql' => 'select id, display_name from column_db_types',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

$column_gui_type_id_relation = 
[
    'relation_sql' => 'select id, display_name from column_gui_types',
    'relation_source_column' => 'id',
    'relation_display_column' => 'display_name',
];

$data_source_rmt_table_id_relation = 
[
    'relation_sql' => 'select id, display_name from data_source_remote_tables',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$data_source_remote_column_id_relation = 
[
    'relation_sql' => 'select id, display_name from data_source_remote_columns',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name_basic',
];

$data_source_col_relation_ids_relation = 
[
    'relation_sql' => 'select id, display_name from data_source_col_relations',
    'relation_source_column' => 'id',
    'relation_display_column' => 'id',
];

$data_source_table_relation_ids_relation = 
[
    'relation_sql' => 'select id, display_name from data_source_tbl_relations',
    'relation_source_column' => 'id',
    'relation_display_column' => 'id',
];

$log_level_id_relation =
[
    'relation_sql' => 'select id, display_name from log_levels',
    'relation_source_column' => 'id',
    'relation_display_column' => 'name',
];


$column_table_relations =
[
    //'auths' => '',
    //'srid' => $srid_relation,
    'user_id' => $user_id_relation,
    'own_id' => $user_id_relation,
    'subscriber_type_id' => 'subscriber_types',
    'up_column_id' => $up_column_id_relation,
    'source_column_id' => $column_id_relation,    
    'relation_source_column_id' => $column_id_relation,
    'data_filter_type_id' => 'data_filter_types',
    'relation_table_id' => $table_id_relation,
    'relation_source_column_id' => $column_id_relation,
    'relation_display_column_id' => $column_id_relation,
    'column_db_type_id' => $column_db_type_id_relation,
    'column_gui_type_id' => $column_gui_type_id_relation,
    'column_table_relation_id' => $column_table_relation_id_relation,
    'subscriber_ids' => $subscriber_ids_relation,
    'column_validation_ids' => $column_validation_ids_relation,
    'column_ids' => $column_id_relation,
    'column_id' => $column_id_relation,
    'control_column_ids' => $column_id_relation,
    'table_id' => $table_id_relation,
    'table_ids' => $table_id_relation,
    'column_array_ids' => $column_array_ids_relation,
    'column_set_type_id' => $column_set_type_id_relation,
    'color_class_id' => 'color_classes',
    //'column_group_ids' => $column_group_ids_relation,
    'join_table_ids' => $join_table_ids_relation,
    'join_table_id' => $table_id_relation,
    'join_column_id' => $column_id_relation,
    'department_id' => $department_id_relation,
    'manager_id' => $user_id_relation,
    'column_array_type_id' => $column_array_type_id_relation,
    'column_data_source_id' => 'column_data_sources',
    'column_gui_trigger_ids' => $column_gui_trigger_ids_relation,
    'column_collective_info_id' => $column_collective_info_id_relation,
    
    'sub_table_id' => $table_id_relation,
    
    'sub_point_type_id' => 'sub_point_types',
    'sub_linestring_type_id' => 'sub_linestring_types',
    'sub_polygon_type_id' => 'sub_polygon_types',
    
    'custom_layer_type_id' => 'custom_layer_types',
    'layer_style_id' => 'layer_styles',
    
    'data_source_type_id' => 'data_source_types',
    'data_source_id' => 'data_sources',
    
    'data_source_rmt_table_id' => $data_source_rmt_table_id_relation,
    'data_source_remote_column_id' => $data_source_remote_column_id_relation,
    
    'data_source_direction_id' => 'data_source_directions',
    
    'data_source_col_relation_ids' => $data_source_col_relation_ids_relation,
    'data_source_table_relation_ids' => $data_source_table_relation_ids_relation,
    
    'log_level_id' => $log_level_id_relation,
];