<?php 
use App\BaseModel;

if(!is_array($column_table_relations[$column->name]))
    $column_table_relations[$column->name] = 
        $this->get_type_column_relation_data(
            $tables, 
            $columns, 
            $column_table_relations[$column->name]);


$temp = $this->get_base_record();
$temp = array_merge($column_table_relations[$column->name], $temp);
$temp['name_basic'] = $column_name_display_name_map[$column->name] . ' kolonu varsayilan tablo ilişkisi';

$column_table_relations[$column->name] = new BaseModel('column_table_relations', $temp);
$column_table_relations[$column->name]->save();

echo "\t\tColumn Table Relations OK: " . $column->name . "\n";