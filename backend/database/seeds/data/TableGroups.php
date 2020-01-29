<?php
use App\BaseModel;

$order = 1;

$table_groups = 
[
    [
        'name' => 'Sistem',
        'table_ids' => 
        [
            $tables['settings']->id,
            $tables['color_classes']->id,
            $tables['table_groups']->id
        ],
        'image' => json_decode('[{"disk": "uploads", "file_name": "settings.png", "destination_path": "uploads/2020/01/01/"}]'),
        'icon' => 'zmdi-settings',
        'order' => $order++
    ],
    [
        'name' => 'İnsan Kaynakları',
        'table_ids' => 
        [            
            $tables['users']->id,
            $tables['departments']->id
        ],
        'image' => json_decode('[{"disk": "uploads", "file_name": "hr.png", "destination_path": "uploads/2020/01/01/"}]'),
        'icon' => 'zmdi-accounts',
        'order' => $order++
    ],
    [
        'name' => 'Tablolar ve Kolonlar',
        'table_ids' => 
        [
            $tables['column_db_types']->id,
            $tables['column_gui_types']->id,
            $tables['join_tables']->id,
            $tables['column_data_sources']->id,
            $tables['column_table_relations']->id,
            $tables['validations']->id,
            $tables['column_validations']->id,
            $tables['column_collective_infos']->id,
            $tables['subscriber_types']->id,
            $tables['subscribers']->id,
            $tables['column_gui_triggers']->id,
            $tables['columns']->id,
            $tables['up_columns']->id,
            $tables['tables']->id
        ],
        'image' => json_decode('[{"disk": "uploads", "file_name": "tables.png", "destination_path": "uploads/2020/01/01/"}]'),
        'icon' => 'zmdi zmdi-grid',
        'order' => $order++
    ],
    [
        'name' => 'Yetkiler',
        'table_ids' => 
        [            
            $tables['data_filter_types']->id,
            $tables['data_filters']->id,
            $tables['column_array_types']->id,
            $tables['column_arrays']->id,
            $tables['column_set_types']->id,
            $tables['column_sets']->id,
            $tables['auth_groups']->id
        ],
        'image' => json_decode('[{"disk": "uploads", "file_name": "auths.png", "destination_path": "uploads/2020/01/01/"}]'),
        'icon' => 'zmdi zmdi-lock',
        'order' => $order++
    ],
    [
        'name' => 'Proje Alt Nesneleri',
        'table_ids' => 
        [            
            $tables['sub_tables']->id,
            $tables['sub_point_types']->id,
            $tables['sub_linestring_types']->id,
            $tables['sub_polygon_types']->id,
            $tables['sub_points']->id,
            $tables['sub_linestrings']->id,
            $tables['sub_polygons']->id
        ],
        'image' => json_decode('[{"disk": "uploads", "file_name": "subfeatures.png", "destination_path": "uploads/2020/01/01/"}]'),
        'icon' => 'zmdi-pin-drop',
        'order' => $order++
    ],
    [
        'name' => 'Harita',
        'table_ids' => 
        [            
            $tables['layer_styles']->id,
            $tables['custom_layer_types']->id,
            $tables['custom_layers']->id,
            $tables['external_layers']->id
        ],
        'image' => json_decode('[{"disk": "uploads", "file_name": "map.png", "destination_path": "uploads/2020/01/01/"}]'),
        'icon' => 'zmdi-map',
        'order' => $order++
    ],
    [
        'name' => 'Veri Aktarıcı',
        'table_ids' => 
        [            
            $tables['data_source_types']->id,
            $tables['data_source_directions']->id,
            $tables['data_sources']->id,
            $tables['data_source_remote_tables']->id,
            $tables['data_source_remote_columns']->id,
            $tables['data_source_column_relations']->id,
            $tables['data_source_table_relations']->id,
            $tables['data_entegrator_errors']->id
        ],
        'image' => json_decode('[{"disk": "uploads", "file_name": "subfeatures.png", "destination_path": "uploads/2020/01/01/"}]'),
        'icon' => 'zmdi-repeat',
        'order' => $order++
    ],
];

$temp = $this->get_base_record();

foreach($table_groups as $i => $table_group)
{
    $table_group = array_merge($temp, $table_group);
    $table_groups[$i] = new BaseModel('table_groups', $table_group);
    $table_groups[$i]->save();
}