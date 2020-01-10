<?php
use App\BaseModel;

$robotUserId = ROBOT_USER_ID;


$relation = new BaseModel('column_table_relations');
$relation->name_basic = $params->display_name . ' kolonu varsayılan tablo ilişkisi';
$relation->relation_table_id = $params->id;
$relation->relation_source_column_id = get_attr_from_cache('columns', 'name', 'id', 'id');
$relation->relation_display_column_id = get_attr_from_cache('columns', 'name', 'name', 'id');
$relation->state = TRUE;
$relation->own_id = $robotUserId;
$relation->user_id = $robotUserId;
$relation->save();


$single = new BaseModel('columns');
$single->name = $params->name.'_id';
$single->display_name = $params->display_name;
$single->column_db_type_id = get_attr_from_cache('column_db_types', 'name', 'integer', 'id');
$single->column_gui_type_id = get_attr_from_cache('column_gui_types', 'name', 'select', 'id');
$single->column_table_relation_id = $relation->id;
$single->column_validation_ids =
[
    get_attr_from_cache('column_validations', 'validation_with_params', 'nullable', 'id'),
    get_attr_from_cache('column_validations', 'validation_with_params', 'integer', 'id'),
    get_attr_from_cache('column_validations', 'validation_with_params', 'numeric_min:1', 'id')
];
$single->state = TRUE;
$single->own_id = $robotUserId;
$single->user_id = $robotUserId;
$single->save();


$multi = new BaseModel('columns');
$multi->name = $params->name.'_ids';
$multi->display_name = $params->display_name.'(lar)';
$multi->column_db_type_id = get_attr_from_cache('column_db_types', 'name', 'jsonb', 'id');
$multi->column_gui_type_id = get_attr_from_cache('column_gui_types', 'name', 'multiselect', 'id');
$multi->column_table_relation_id = $relation->id;
$multi->column_validation_ids =
[
    get_attr_from_cache('column_validations', 'validation_with_params', 'nullable', 'id'),
    get_attr_from_cache('column_validations', 'validation_with_params', 'json', 'id')
];
$multi->state = TRUE;
$multi->own_id = $robotUserId;
$multi->user_id = $robotUserId;
$multi->save();