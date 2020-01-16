<?php
use App\BaseModel;

$tables[$table] = [];
$tables[$table]['name'] = $table;
$tables[$table]['display_name'] = $table_name_display_name_map[$table];
$tables[$table]['column_ids'] = [];

foreach($table_columns as $column)
    array_push($tables[$table]['column_ids'], $columns[$column['name']]->id);
     
if(isset($subscribers['table'][$table]))
{
    $tables[$table]['subscriber_ids'] = [];
    foreach($subscribers['table'][$table] as $sub)
        array_push($tables[$table]['subscriber_ids'], $sub->id);
}
$temp = $this->get_base_record();
$temp = array_merge($tables[$table], $temp);

$tables[$table] = new BaseModel('tables', $temp);
$tables[$table]->save();

echo "Table OK: " . $table ."\n\n\n";



if(isset($join_tables[$table]))
{
    foreach($join_tables[$table] as $ii => $join_table)
    {
        if(!is_numeric($join_table['join_table_id']))
            $join_table['join_table_id'] = $tables[$join_table['join_table_id']]->id;
        
        if(!is_numeric($join_table['join_column_id']))
            $join_table['join_column_id'] = $columns[$join_table['join_column_id']]->id;

        $temp = $this->get_base_record();
        $temp = array_merge($join_table, $temp);

        $join_tables[$table][$ii] = new BaseModel('join_tables', $temp);
        $join_tables[$table][$ii]->save();
    }
}

if(isset($column_arrays[$table]))
   foreach($column_arrays[$table] as $iii => $column_array)
    {
        if(!is_numeric($column_array['table_id']))
            $column_array['table_id'] = $tables[$column_array['table_id']]->id;
        
        foreach($column_array['column_ids'] as $jj => $columnName)
            if(!is_numeric($columnName))
                if(!is_numeric($columnName))
                    $column_array['column_ids'][$jj] = $columns[$columnName]->id;
            
        foreach($column_array['join_table_ids'] as $iiii => $jti)
            $column_array['join_table_ids'][$iiii] = $join_tables[$table][$jti]->id;
                    
        $temp = $this->get_base_record();
        $temp = array_merge($column_array, $temp);

        $column_arrays[$table][$iii] = new BaseModel('column_arrays', $temp);
        $column_arrays[$table][$iii]->save();
    }
    
/*if(isset($column_groups[$table]))
   foreach($column_groups[$table] as $jj => $column_group)
    {
        foreach($column_group['column_array_ids'] as $jjj => $cai)
            $column_group['column_array_ids'][$jjj] = $column_arrays[$table][$cai]->id;
        
        $column_group['color_class_id'] = $color_classes[$column_group['color_class_id']]->id;
        
        $temp = $this->get_base_record();
        $temp = array_merge($column_group, $temp);

        $column_groups[$table][$jj] = new BaseModel('column_groups', $temp);
        $column_groups[$table][$jj]->save();
    }*/
    
if(isset($column_sets[$table]))
    foreach($column_sets[$table] as $jjj => $column_set)
    {
        if(!is_numeric($column_set['table_id']))
            $column_set['table_id'] = $tables[$column_set['table_id']]->id;
        
        $column_set['column_set_type_id'] = $column_set_types[$column_set['column_set_type_id']]->id;
        
        foreach($column_set['column_array_ids'] as $jjjj => $cgi)
            $column_set['column_array_ids'][$jjjj] = $column_arrays[$table][$cgi]->id;
        
        $temp = $this->get_base_record();
        $temp = array_merge($column_set, $temp);
        
        $column_sets[$table][$jjj] = new BaseModel('column_sets', $temp);
        $column_sets[$table][$jjj]->save();
    }

echo "Table Column Arrays OK: " . $table ."\n\n\n";