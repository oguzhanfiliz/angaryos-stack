<?php

use App\BaseModel;

$defaultCounts = 
[
    //'settings' => 3,
    //'validations' => 4,
    //'data_filter_types' => 7,
    /*'data_filters' => 75,
    'join_tables' => 10,
    'column_gui_triggers' => 5,
    'column_array_types' => 2,
    'column_arrays' => 5,
    'column_validations' => 20,
    'column_gui_types' => 28,
    'column_gui_triggers' => 1,
    'column_set_types' => 6,
    'column_sets' => 1,
    'column_table_relations' => 40,
    'column_collective_infos' => 5,
    'column_data_sources' => 1,
    'column_db_types' => 16,
    'columns' => 61,
    'users' => 0,
    'departments' => 0,
    'color_classes' => 6,
    'tables' => 25,
    'subscriber_types' => 2,
    'subscribers' => 3,
    'auth_groups' => 26*/
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
    ];

if(!isset($data_filters['data_filters'])) $data_filters['data_filters'] = [];

/*$temp =
[
    [
        'name_basic' => 'Varsayılan filtreler görülemesin',
        'data_filter_type_id' => $data_filter_types['list']->id,
        'sql_code' => 'TABLE.id > 5'
    ],
];
$data_filters['data_filters'] = array_merge($temp, $data_filters['data_filters']);*/

$i = 0;
$data_filters['bos'] =
[
    [
        'name_basic' => 'Dışa aktarma engelleme',
        'data_filter_type_id' => $data_filter_types['export']->id,
        'sql_code' => 'FALSE'
    ],
];

/*$data_filters['departments'] = 
[
    [
        'name' => 'Sadece ikiye bölünemeyen idler gelsin',
        'data_filter_type_id' => $data_filter_types['list']->id,
        'sql_code' => 'TABLE.id % 2 != 0'
    ],
    [
        'name' => 'Sadece 1 id li kayıt düzenlenebilsin',
        'data_filter_type_id' => $data_filter_types['update']->id,
        'sql_code' => 'TABLE.id = 1'
    ],
    [
        'name' => 'Sadece adında "bilgi" geçen kayıt düzenlenebilsin',
        'data_filter_type_id' => $data_filter_types['update']->id,
        'sql_code' => 'TABLE.name ilike \'%bilgi%\''
    ],
    [
        'name' => 'Sadece 2 id li kayıt silinebilsin',
        'data_filter_type_id' => $data_filter_types['delete']->id,
        'sql_code' => 'TABLE.id = 2'
    ],
    [
        'name' => 'Sadece idsi 2 den büyük kayıt geri yüklenebilsin',
        'data_filter_type_id' => $data_filter_types['restore']->id,
        'sql_code' => 'TABLE.id > 2'
    ],
    [
        'name' => '"mudur" kolonunda "adm" geçen kayıtların detayına bakılabilsin',
        'data_filter_type_id' => $data_filter_types['show']->id,
        'sql_code' => 'string_agg(concat(mudur.name_basic, \'-\', mudur.surname), \',\') ilike \'%adm%\''
    ],
    [
        'name' => 'Sadece 1 id li kayıt dışa aktarılabilsin',
        'data_filter_type_id' => $data_filter_types['export']->id,
        'sql_code' => 'TABLE.id = 1'
    ]
];*/

foreach($data_filters as $tableName => $filters)
    foreach($filters as $kk => $filter)
    {
        $temp = $this->get_base_record();
        $temp = array_merge($temp, $filter);

        $data_filters[$tableName][$kk] = new BaseModel('data_filters', $temp);
        $data_filters[$tableName][$kk]->save();
    }