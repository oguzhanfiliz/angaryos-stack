<?php

$up_columns['column_ids'] =
[
    'name_basic' => 'Sadece seçili tablonun kolonları gelsin',
    'column_id' => 60,//'table_id',//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    
    
    //Bu $columnName ile aynı olmak zorunda değil. Şimdi böyle denk geldi. //$up_columns[$columnName]
    'source_column_id' => 58,//'column_ids'//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
    
    
    'table_ids' => [/*'column_arrays'*/21]//Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
];
$up_columns['column_id'] = $up_columns['column_ids'];