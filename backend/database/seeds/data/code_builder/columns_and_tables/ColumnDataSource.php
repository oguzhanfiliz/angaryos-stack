<?php 
use App\BaseModel;

$temp = $this->get_base_record();
$temp['name'] = $column_name_display_name_map[$column->name] . ' veri kaynağı';
$temp['php_code'] = $column_data_sources[$column->name];

$column_data_sources[$column->name] = new BaseModel('column_data_sources', $temp);
$column_data_sources[$column->name]->save();


$temp = $this->get_base_record();
$temp['name_basic'] = $column->name . ' varsayilan';
$temp['column_data_source_id'] = $column_data_sources[$column->name]->id;

$column_table_relations[$column->name] = new BaseModel('column_table_relations', $temp);
$column_table_relations[$column->name]->save();


echo "\t\tColumn Data Source and Table Relations OK: " . $column->name . "\n";