<?php

$join_tables['columns'] = 
[
    [
        //'name' => 'kolonun_db_tipi_iliskisi',
        //'display_name' => 'Kolonun DB tipi ilişkisi',
        'name' => 'Kolonun DB tipi ilişkisi',
        
        'join_table_id' => 'column_db_types',//$tables['column_db_types']->id,
        'join_table_alias' => 'kolon_db_tipleri',
        
        'connection_column_with_alias' => 'columns.column_db_type_id',
        'join_connection_type' => '=',
        'join_column_id' => 'id',//$column['id']->id
    ],
    [
        //'name' => 'db_tipini_ekleyen_kullanici_iliskisi',
        //'display_name' => 'DB tipini ekleyen kullanıcı ilişkisi',
        'name' => 'DB tipini ekleyen kullanıcı ilişkisi',
        
        'join_table_id' => 'users',
        'join_table_alias' => 'db_tipini_ekleyen_kullanicilar',
        
        'connection_column_with_alias' => 'kolon_db_tipleri.user_id',
        'join_connection_type' => '=',
        'join_column_id' => 'id',
    ],
    [
        //'name' => 'bu_kolonu_iceren_tablolar_iliskisi',
        //'display_name' => 'Bu kolonu ekleyen tablolar ilişkisi',
        'name' => 'Bu kolonu ekleyen tablolar ilişkisi',
        
        'join_table_id' => 19,//'tables', Tablo yada kolon eklenme sıralaması değişirse yeni id yazılması gerekir
        'join_table_alias' => 'kolonu_iceren_tablo',
        
        'connection_column_with_alias' => 'columns.id',
        'join_connection_type' => '=',
        'join_column_id' => 57//'column_ids' Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    ],
    [
        //'name' => 'kolonun_db_tipi_iliskisi',
        //'display_name' => 'Kolonun DB tipi ilişkisi',
        'name' => 'Tablonun takipçileri ilişkisi',
        
        'join_table_id' => 'subscribers',//$tables['subscrebers']->id,
        'join_table_alias' => 'tablo_takipcileri',
        
        'connection_column_with_alias' => 'tables.subscriber_ids',
        'join_connection_type' => '=',
        'join_column_id' => 'id',//$column['id']->id
    ],
];


$column_arrays['columns'] =
[
    [
        //'name' => 'test_icin_kolonlar_tablosu_kolon_seti',
        //'display_name' => 'Test için kolonlar tablosu kolon seti',
        'name' => '',
        'column_array_type_id' => $column_array_types['direct_data']->id,
        'table_id' => 'columns',
        'column_ids' => ['id', 'name', 'display_name'],
        'join_table_ids' => [0, 1],
        'join_columns' => 'string_agg(kolon_db_tipleri.name, \',\') as db_tipi, string_agg(concat(db_tipini_ekleyen_kullanicilar.name, \'-\', db_tipini_ekleyen_kullanicilar.surname), \',\') as description',
    ],
    [
        //'name' => 'test_icin_kolonlar_tablosu_bu_kolonu_iceren_tablolar_seti',
        //'display_name' => 'Test için kolonlar tablosu bu kolonu içeren tablolar seti',
        'name' => 'Bu kolonu içeren tablolar',
        'column_array_type_id' => $column_array_types['table_from_data']->id,
        'table_id' => 'columns',
        'column_ids' => ['id', 'name', /*'column_ids'*/57],//'column_ids' Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
        'join_table_ids' => [2, 3],
        'join_columns' => 'string_agg(tablo_takipcileri.name, \',\') as takipciler',
    ],
    [
        //'name' => 'test_icin_kolonlar_tablosu_guncellenme_bilgileri_seti',
        //'display_name' => 'Test için kolonlar tablosu güncellenme bilgileri seti',
        'name' => 'Güncelleme bilgileri',
        'column_array_type_id' => $column_array_types['direct_data']->id,
        'table_id' => 'columns',
        'column_ids' => ['user_id', 'updated_at'],
        'join_table_ids' => [],
        'join_columns' => NULL,
    ],
    [
        //'name' => 'test_icin_kolonlar_tablosu_eklenme_bilgileri_seti',
        //'display_name' => 'Test için kolonlar tablosu eklenme bilgileri seti',
        'name' => 'Ekleme bilgileri',
        'column_array_type_id' => $column_array_types['direct_data']->id,
        'table_id' => 'columns',
        'column_ids' => ['own_id', 'created_at'],
        'join_table_ids' => [],
        'join_columns' => NULL,
    ]
];

/*$column_groups['columns'] =
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
        'column_array_ids' => [3],
        'color_class_id' => 'secondary'
    ]
];*/

$column_sets['columns'] =
[
    [
        //'name' => 'test_icin_kolonlar_tablosu_kolon_seti',
        //'display_name' => 'Test için kolonlar tablosu kolon seti',
        'name' => 'Yeni Kolon Seti',
        'table_id' => 'columns',
        'column_set_type_id' => 'none',
        'column_array_ids' => [0, 1, 2, 3]
    ]
];
