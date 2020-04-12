<?php

use App\BaseModel;

$column_arrays = [];
//$column_groups = [];
$column_sets = [];
$join_tables = [];

require 'FillVariables.php';

$tables = [];
$columns = [];

$writed_columns = [];

$tableDBOperationHelper = new \App\Libraries\TableDBOperationsLibrary();

$tempColumn = 
[
    'name' => 'all',
    'display_name' => 'Tümü',
    'column_db_type_id' => get_attr_from_cache('column_db_types', 'name', 'string', 'id'),
    'column_gui_type_id' => get_attr_from_cache('column_gui_types', 'name', 'string', 'id')
];
$temp = $this->get_base_record();
$temp = array_merge($tempColumn, $temp);

$columns['all'] = new BaseModel('columns', $temp);
$columns['all']->save();

$tempColumn = 
[
    'name' => 'own',
    'display_name' => 'Kendisi',
    'column_db_type_id' => get_attr_from_cache('column_db_types', 'name', 'string', 'id'),
    'column_gui_type_id' => get_attr_from_cache('column_gui_types', 'name', 'string', 'id')
];
$temp = $this->get_base_record();
$temp = array_merge($tempColumn, $temp);
$columns['own'] = new BaseModel('columns', $temp);
$columns['own']->save();

$tempColumn = 
[
    'name' => 'record_id',
    'display_name' => 'Kayıt No',
    'column_db_type_id' => 5,
    'column_gui_type_id' => 3,
    
];
$temp = $this->get_base_record();
$temp = array_merge($tempColumn, $temp);
$columns['record_id'] = new BaseModel('columns', $temp);
$columns['record_id']->save();

$tempColumn = 
[
    'name' => 'remote_record_id',
    'display_name' => 'Uzak Kayıt ID',
    'column_db_type_id' => get_attr_from_cache('column_db_types', 'name', 'integer', 'id'),
    'column_gui_type_id' => get_attr_from_cache('column_gui_types', 'name', 'numeric', 'id')
];
$temp = $this->get_base_record();
$temp = array_merge($tempColumn, $temp);

$columns['remote_record_id'] = new BaseModel('columns', $temp);
$columns['remote_record_id']->save();

$tempColumn = 
[
    'name' => 'disable_entegrate',
    'display_name' => 'Veri Aktarma Devre Dışı',
    'column_db_type_id' => get_attr_from_cache('column_db_types', 'name', 'boolean', 'id'),
    'column_gui_type_id' => get_attr_from_cache('column_gui_types', 'name', 'boolean', 'id')
];
$temp = $this->get_base_record();
$temp = array_merge($tempColumn, $temp);

$columns['disable_entegrate'] = new BaseModel('columns', $temp);
$columns['disable_entegrate']->save();



/*$requiredColumns = 
[
    'name' => 
    [
        'name' => 'name',
        'type' => 'character varying',
        'srid' => NULL
    ]
];*/

foreach(array_keys($table_name_display_name_map) as $table)
{
    $table_columns = helper('get_all_columns_from_db', $table);
    //$table_columns = array_merge($table_columns, $requiredColumns);
    
    foreach($table_columns as $columnName => $column)
    {
        $column = (Object)$column;
        
        if(in_array($column->name, $writed_columns)) continue;
        array_push($writed_columns, $column->name);
        
        echo "\tColumn insert started: " . $column->name."\n";
        
        if(isset($column_table_relations[$column->name]))
            require 'ColumnTableRelation.php';
        
        if(isset($column_data_sources[$column->name]))
            require 'ColumnDataSource.php';
        
        if(isset($up_columns[$column->name]))
            require 'UpColumn.php';
        
        
        
        require 'Column.php';
    }
    
    require 'Table.php';
}

foreach(array_keys($table_name_display_name_map) as $table)
{
    $tableDBOperationHelper->CreateArchiveTableOnDB($table, $table.'_archive');
    //helper('clone_table_on_db', [$table, $table.'_archive']);
    
    echo "Archive Table OK: " . $table ."\n\n\n";
}

$tempRelationTable = 
[
    'name_basic' => 'Filtreler kolonu varsayılan tablo ilişkisi',
    'relation_table_id' => get_attr_from_cache('tables', 'name', 'data_filters', 'id'),
    'relation_source_column_id' => get_attr_from_cache('columns', 'name', 'id', 'id'),
    'relation_display_column_id' => get_attr_from_cache('columns', 'name', 'name_basic', 'id')
];
$temp = $this->get_base_record();
$temp = array_merge($tempRelationTable, $temp);

$tempRelationTable = new BaseModel('column_table_relations', $temp);
$tempRelationTable->save();

$tempColumn = 
[
    'name' => 'data_filter_ids',
    'display_name' => 'Data Filtre(ler)i',
    'column_db_type_id' => get_attr_from_cache('column_db_types', 'name', 'jsonb', 'id'),
    'column_gui_type_id' => get_attr_from_cache('column_gui_types', 'name', 'multiselect', 'id'),
    'column_table_relation_id' => $tempRelationTable->id
];
$temp = $this->get_base_record();
$temp = array_merge($tempColumn, $temp);

$columns['data_filter_ids'] = new BaseModel('columns', $temp);
$columns['data_filter_ids']->save();




$tempRelationTable = 
[
    'name_basic' => 'Kolon setleri kolonu varsayılan tablo ilişkisi',
    'relation_table_id' => get_attr_from_cache('tables', 'name', 'column_sets', 'id'),
    'relation_source_column_id' => get_attr_from_cache('columns', 'name', 'id', 'id'),
    'relation_display_column_id' => get_attr_from_cache('columns', 'name', 'name_basic', 'id')
];
$temp = $this->get_base_record();
$temp = array_merge($tempRelationTable, $temp);

$tempRelationTable = new BaseModel('column_table_relations', $temp);
$tempRelationTable->save();

$tempColumn = 
[
    'name' => 'column_set_ids',
    'display_name' => 'Kolon Set(ler)i',
    'column_db_type_id' => get_attr_from_cache('column_db_types', 'name', 'jsonb', 'id'),
    'column_gui_type_id' => get_attr_from_cache('column_gui_types', 'name', 'multiselect', 'id'),
    'column_table_relation_id' => $tempRelationTable->id
];
$temp = $this->get_base_record();
$temp = array_merge($tempColumn, $temp);

$columns['column_set_ids'] = new BaseModel('columns', $temp);
$columns['column_set_ids']->save();