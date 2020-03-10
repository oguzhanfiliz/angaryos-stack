<?php

$up_columns['column_ids'] =
[
    'name_basic' => 'sadece seçili tablonun kolonları gelsin',
    'column_id' => 60,//'table_id',//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    
    ///*'column_arrays'*/21 vardı iptal edildi. kolon dizilerinde join tablo eklenir ise başk tablolardan da kolon seçmek gerekebilir
    'table_ids' => [],////Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    
    'php_code' => '<?php 
$return = get_attr_from_cache(\'tables\', \'id\', (int)$data, \'column_ids\');
$return = json_decode($return);
?>'
];
$up_columns['column_id'] = $up_columns['column_ids'];

$up_columns['data_source_rmt_table_id'] =
[
    'name_basic' => 'sadece seçili veri kaynağının tabloları gelsin',
    'column_id' => 86,//'data_source_id',//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    'table_ids' => [/*'data_source_tbl_relations'*/43],//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    'php_code' => '<?php
$temp = DB::table(\'data_source_remote_tables\')
            ->where(\'data_source_id\', (int)$data)
            ->pluck(\'id\');
$return = [];
foreach($temp as $item) array_push($return, $item);
?>'
];

$up_columns['column_array_ids'] =
[
    'name_basic' => 'sadece seçili tablonun dizileri gelsin',
    'column_id' => 60,//'table_id',//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    'table_ids' => [/*'column_arrays'*/21],//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    'php_code' => '<?php
$temp = DB::table(\'column_arrays\')
            ->where(\'table_id\', (int)$data)
            ->pluck(\'id\');
$return = [];
foreach($temp as $item) array_push($return, $item);
?>'
];

//**:join ile başka tablolardaki kolonlar da kolon dizisine eklenmek istenebilir