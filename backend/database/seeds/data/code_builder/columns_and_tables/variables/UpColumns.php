<?php

$up_columns['column_ids'] =
[
    'name_basic' => 'sadece seçili tablonun kolonları gelsin',
    'column_id' => 66,//'table_id',//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    
    ///*'column_arrays'*/21 vardı iptal edildi. kolon dizilerinde join tablo eklenir ise başk tablolardan da kolon seçmek gerekebilir
    'table_ids' => [],////Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    
    'php_code' => '<?php 

$columnArrayTypeId = @$request[\'column_array_type_id\'];
if(strlen($columnArrayTypeId) == 0) $columnArrayTypeId = @$record[\'column_array_type_id\'];

global $pipe;
if($pipe[\'table\'] == \'column_arrays\' &&  (int)$columnArrayTypeId == 2)
{
    $return = \'***\';
    return;
}

if(!isset($request[\'join_table_ids\']))
{
    $id = (int)read_from_response_data(\'editRecordId\');
    
    if($id == 0) $request[\'join_table_ids\'] = [];
    else
    {
        $temp = \DB::table($pipe[\'table\'])->find($id);
        $request[\'join_table_ids\'] = json_decode($temp->join_table_ids);
        if($request[\'join_table_ids\'] == null) $request[\'join_table_ids\'] = [];   
    }
}

if(count($request[\'join_table_ids\']) > 0)
{
    $return = \'***\';
    return;
}

$return = get_attr_from_cache(\'tables\', \'id\', (int)$data, \'column_ids\');
$return = json_decode($return);

?>'
];
$up_columns['column_id'] = $up_columns['column_ids'];

$up_columns['data_source_rmt_table_id'] =
[
    'name_basic' => 'sadece seçili veri kaynağının tabloları gelsin',
    'column_id' => 92,//'data_source_id',//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
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
    'column_id' => 66,//'table_id',//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
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