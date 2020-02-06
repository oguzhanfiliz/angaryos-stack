<?php

$join_tables['departments'] = 
[
    [
        'name' => 'manager_id kolonu üzerinden kullanıcılar tablosu bağlantısı',
        
        'join_table_id' => 2,//users,
        'join_table_alias' => 'mudur',
        
        'connection_column_with_alias' => 'departments.manager_id',
        //'join_connection_type' => '=',
        'join_column_id' => 'id',//$column['id']->id
    ],
    [
        'name' => 'bağlantı yapılmış kullanıcılar tablosundaki own_id kolonu üzerinden kullanıcılar tablosu bağlantısı',
        
        'join_table_id' => 2,//'user', Tablo yada kolon eklenme sıralaması değişirse yeni id yazılması gerekir
        'join_table_alias' => 'mudur_kaydini_ekleyen_kullanici',
        
        'connection_column_with_alias' => 'mudur.user_id',
        //'join_connection_type' => '=',
        'join_column_id' => 'id',
    ],
    //Bu özel join. Tablo buradan başlıyor. Bilgi kartı içinde liste göstermek için
    [
        'name' => 'Bilgi kartında gösterilecek olan bu müdürlükteki personeller tablosu için ilk join',
        
        'join_table_id' => 2,//'user', Tablo yada kolon eklenme sıralaması değişirse yeni id yazılması gerekir
        'join_table_alias' => 'mudurlukteki_personeller',
        
        'connection_column_with_alias' => 'departments.id',
        //'join_connection_type' => '=',
        'join_column_id' => 16//'department_id' Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    ],
    [
        'name' => 'Bilgi kartında gösterilecek olan bu müdürlükteki personeller tablosu için ikinci join',
        
        'join_table_id' => 1,//'departments', Tablo yada kolon eklenme sıralaması değişirse yeni id yazılması gerekir
        'join_table_alias' => 'personelin_mudurlugu',
        
        'connection_column_with_alias' => 'users.department_id',
        //'join_connection_type' => '=',
        'join_column_id' => 'id'
    ]
];


$column_arrays['departments'] =
[
    [
        'name' => '',
        'column_array_type_id' => $column_array_types['direct_data']->id,
        'table_id' => 'departments',
        'column_ids' => ['id', 'name', 'manager_id', 'state', 'user_id', 'updated_at'],
        'join_table_ids' => [0, 1],
        'join_columns' => 
                            'string_agg(concat(mudur.name, \'-\', mudur.surname), \',\') as mudur, '
                            .'string_agg(mudur_kaydini_ekleyen_kullanici.name, \',\') as description, '
                            .'string_agg(mudur_kaydini_ekleyen_kullanici.name, \',\') as db_type_id, '
                            .'string_agg(mudur_kaydini_ekleyen_kullanici.name, \',\') as test_yeni_isim, ',
    ],
    [
        'name' => 'Bu müdürlüğün personelleri',
        'column_array_type_id' => $column_array_types['table_from_data']->id,
        'table_id' => 'departments',
        'column_ids' => ['id', 'name_basic', /*'surname'*/15, 'own_id', /*'auths'*/21],//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
        'join_table_ids' => [2, 3],
        'join_columns' => 'string_agg(personelin_mudurlugu.name, \',\') as iliskiden_personelin_md',
    ],
    [
        'name' => '',
        'column_array_type_id' => $column_array_types['direct_data']->id,
        'table_id' => 'departments',
        'column_ids' => ['own_id', 'created_at'],
        'join_table_ids' => [],
        'join_columns' => '',
    ],
    [
        'name' => 'adım form 1. adım',
        'column_array_type_id' => $column_array_types['direct_data']->id,
        'table_id' => 'departments',
        'column_ids' => ['name', 'manager_id'],
        'join_table_ids' => [2, 3],//test - join kolonlar es geçilmeli!
        'join_columns' => 'string_agg(personelin_mudurlugu.name, \',\') as iliskiden_personelin_md',//es geçilmeli
    ],
    [
        'name' => 'adım form 2. adım',
        'column_array_type_id' => $column_array_types['direct_data']->id,
        'table_id' => 'departments',
        'column_ids' => ['description', 'state'],
        'join_table_ids' => [],
        'join_columns' => '',
    ]
];

/*$column_groups['departments'] =
[
    [
        //'name' => 'test_icin_kolonlar_tablosu_birinci_grup',
        //'display_name' => 'Test için kolonlar tablosu birinci grup',
        'name' => 'Karışık Data',
        'column_array_ids' => [0, 1, 2],
        'color_class_id' => 'info'
    ],
    [
        //'name' => 'test_icin_kolonlar_tablosu_ikinci_grup',
        //'display_name' => 'Test için kolonlar tablosu ikinci grup',
        'name' => 'Direkt Data',
        'column_array_ids' => [2],
        'color_class_id' => 'secondary'
    ],
    [
        //'name' => 'test_icin_kolonlar_tablosu_ikinci_grup',
        //'display_name' => 'Test için kolonlar tablosu ikinci grup',
        'name' => 'birinci adım',
        'column_array_ids' => [3],
        'color_class_id' => 'secondary'
    ],
    [
        //'name' => 'test_icin_kolonlar_tablosu_ikinci_grup',
        //'display_name' => 'Test için kolonlar tablosu ikinci grup',
        'name' => 'ikinci adım',
        'column_array_ids' => [4],
        'color_class_id' => 'secondary'
    ]
];*/

$column_sets['departments'] =
[
    [
        //'name' => 'test_icin_kolonlar_tablosu_kolon_seti',
        //'display_name' => 'Test için kolonlar tablosu kolon seti',
        'name' => 'Yeni Kolon Seti',
        'table_id' => 'departments',
        'column_set_type_id' => 'none',
        'column_group_ids' => [0, 1, 2]
    ],
    [
        //'name' => 'test_icin_kolonlar_tablosu_kolon_seti',
        //'display_name' => 'Test için kolonlar tablosu kolon seti',
        'name' => 'Form için adım kolon seti',
        'table_id' => 'departments',
        'column_set_type_id' => 'seteps',
        'column_group_ids' => [3, 4]
    ]
];