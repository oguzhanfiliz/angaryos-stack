<?php
use App\BaseModel;

/*$defaultCounts = 
[
    //'settings' => 3,
    'validations' => 10,
    'data_filter_types' => 6,
    'data_filters' => 64,
    'join_tables' => 6,
    'column_gui_triggers' => 1,
    'column_array_types' => 2,
    'column_arrays' => 5,
    'column_validations' => 27,
    'column_gui_types' => 22,
    'column_set_types' => 6,
    'column_sets' => 2,
    'column_table_relations' => 45,
    'column_collective_infos' => 5,
    'column_data_sources' => 1,
    'column_db_types' => 16,
    'columns' => 99,
    //'users' => 0,
    //'departments' => 0,
    'color_classes' => 6,
    'tables' => 47,
    'subscriber_types' => 2,
    'subscribers' => 10,
    'auth_groups' => 48
];

foreach($defaultCounts as $tableName => $count)
    $data_filters[$tableName] = 
    [
        [
            'name_basic' => $table_name_display_name_map[$tableName].' tablosu, varsayılan kayıtlar silinemesin',
            'data_filter_type_id' => $data_filter_types['delete']->id,
            'sql_code' => 'TABLE.id > '.$count
        ],
        [
            'name_basic' => $table_name_display_name_map[$tableName].' tablosu, varsayılan kayıtlar düzenlenemesin',
            'data_filter_type_id' => $data_filter_types['update']->id,
            'sql_code' => 'TABLE.id > '.$count
        ],
        [
            'name_basic' => $table_name_display_name_map[$tableName].' tablosu, varsayılan kayıtlar geri yüklenemesin',
            'data_filter_type_id' => $data_filter_types['restore']->id,
            'sql_code' => 'TABLE.id > '.$count
        ]
    ];*/

$data_filters['common'] = 
[
    [
        'name_basic' => 'Kullanıcı sadece kendi kayıtlarını listeyebilsin filtresi',
        'data_filter_type_id' => $data_filter_types['list']->id,
        'sql_code' => '$record->own_id = $user->id'
    ],
    [
        'name_basic' => 'Kullanıcı sadece kendi kayıtlarını silebilsin filtresi',
        'data_filter_type_id' => $data_filter_types['delete']->id,
        'sql_code' => '$record->own_id = $user->id'
    ],
    [
        'name_basic' => 'Kullanıcı sadece kendi kayıtlarını düzenleyebilsin filtresi',
        'data_filter_type_id' => $data_filter_types['update']->id,
        'sql_code' => '$record->own_id = $user->id'
    ],
    [
        'name_basic' => 'Kullanıcı sadece kendi kayıtlarını geri yükleyebilsin filtresi',
        'data_filter_type_id' => $data_filter_types['restore']->id,
        'sql_code' => '$record->own_id = $user->id'
    ],
    [
        'name_basic' => 'Kullanıcı sadece kendi kayıtlarını gösterebilsin filtresi',
        'data_filter_type_id' => $data_filter_types['show']->id,
        'sql_code' => '$record->own_id = $user->id'
    ],
    [
        'name_basic' => 'Kullanıcı sadece kendi kayıtlarını dışa aktarabilsin filtresi',
        'data_filter_type_id' => $data_filter_types['export']->id,
        'sql_code' => '$record->own_id = $user->id'
    ]
];

$filter =
[
    'name_basic' => 'Onaylanmış e-imzalar düzenlenemesin güncelleme filtresi',
    'data_filter_type_id' => $data_filter_types['update']->id,
    'sql_code' => '(("sign_at"::text = \'\') IS NOT FALSE)' 
];

if(!isset($data_filters['e_signs'])) $data_filters['e_signs'] = [];
array_push($data_filters['e_signs'], $filter);

foreach($data_filters as $tableName => $filters)
    foreach($filters as $kk => $filter)
    {
        $temp = $this->get_base_record();
        $temp = array_merge($temp, $filter);

        $data_filters[$tableName][$kk] = new BaseModel('data_filters', $temp);
        $data_filters[$tableName][$kk]->save();
    }