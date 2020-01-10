<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;
use App\BaseModel;
use Cache;

trait BaseModelGetDataColumnTrait 
{    
    private $geometryColumnTypes = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
    
    public function getColumns($model, $tableName, $columnArrayOrSetId)
    {
        switch($tableName)
        {
            case 'column_arrays': return $this->getColumnsByColumnArrayId($model, $columnArrayOrSetId);
            case 'column_sets': return $this->getColumnsByColumnSetId($model, $columnArrayOrSetId);
            default: abort(helper('response_error', 'undefined.type:'.$tableName));  
        }
    }
        
    private function getColumnsByColumnArrayId($model, $id, $form = FALSE)
    {
        if($id == 0) 
            return $this->getAllColumnsFromTable();
        else
            return $this->getAllColumnsFromColumnArray($model, $id);
    }
    
    private function getAllColumnsFromColumnArray($model, $id, $form = FALSE)
    {
        $columnArray = get_attr_from_cache('column_arrays', 'id', $id, '*');
        $columnArray->fillVariables();
        
        $type = $columnArray->getRelationData('column_array_type_id')->name;
        switch($type)
        {
            case 'direct_data': return $this->getAllColumnsFromColumnArrayDirectData($model, $columnArray, $form);
            case 'table_from_data': return $this->getAllColumnsFromColumnArrayTableFromData($model, $columnArray);
            default: abort(helper('response_error', 'undefined.column_array.type:'.$type));  
        }
    }
    
    private function getAllColumnsFromColumnArrayDirectData($model, $columnArray, $form = FALSE)
    {
        $columns = helper('get_null_object');
        foreach($columnArray->getRelationData('column_ids') as $column)
        {
            $column->gui_type_name = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
            $column->db_type_name = get_attr_from_cache('column_db_types', 'id', $column->column_db_type_id, 'name');
            $column->table_alias = $this->getTable();
            
            $columns->{$column->name} = $column;
        }
        if($columnArray->getRelationData('column_array_type_id')->name == 'direct_data')
            foreach($columnArray->getRelationData('join_table_ids') as $join)
                $this->addJoinForColumnArray($model, $join);
        
        if($form) return $columns;
        
        $arr = helper('divide_select', $columnArray->join_columns);
        foreach($arr as $c)
        {
            if(strlen(trim($c)) == 0) continue;
            
            $column = (Object)[];
            $temp = helper('get_column_data_for_joined_column', $c);
            
            $column->id = -1;
            $column->name = $temp[1];
            $column->display_name = 'display '.$temp[1];
            $column->column_table_relation_id = NULL;
            $column->gui_type_name = 'string';
            $column->db_type = 'string?';
            $column->table_alias = '';
            $column->table_name = '';
            $column->select_raw = $c;
            
            $columns->{$temp[1]} = $column;
        }
        
        return $columns;
    }
    
    private function getAllColumnsFromColumnArrayTableFromData($model, $columnArray)
    {
        global $pipe;
        if(!isset($pipe['relation_table_data_request'])) return NULL;
        
        return $this->getAllColumnsFromColumnArrayDirectData($model, $columnArray);
    }
    
    public function getColumnSet($model, $columnSetId, $form = FALSE)
    {
        if($columnSetId == 0) 
            return $this->getColumnSetDefault();
        else
            return $this->getColumnSetByColumnSetId($model, $columnSetId, $form);
    }
    
    private function getColumnSetDefault()
    {
        $set = (Object)[];
        $set->name = '';
        $set->column_set_type = 'none';
        $set->column_groups = [];
        
        $set->column_groups[0] = (Object)[];        
        $set->column_groups[0]->id = 0;
        $set->column_groups[0]->name = '';
        $set->column_groups[0]->color_class = 'none';
        $set->column_groups[0]->column_group_type = 'none';
        $set->column_groups[0]->column_arrays = [];
        
        $set->column_groups[0]->column_arrays[0] = (Object)[];
        $set->column_groups[0]->column_arrays[0]->id = 0;
        $set->column_groups[0]->column_arrays[0]->name = '';
        $set->column_groups[0]->column_arrays[0]->column_array_type = 'direct_data';
        $set->column_groups[0]->column_arrays[0]->columns = $this->getAllColumnsFromTable();
        
        return $set;
    }
    
    private function getColumnSetByColumnSetId($model, $columnSetId, $form)
    {
        $columnSet = new BaseModel('column_sets');
        $columnSet = $columnSet->find($columnSetId);
        
        $temp = $columnSet->getRelationData('column_group_ids');
        
        $set = (Object)[];
        $set->name = $columnSet->name;
        $set->column_set_type = $columnSet->getRelationData('column_set_type_id')->name;
        $set->column_groups = [];
        
        foreach($temp as $i => $columnGroup)
        {
            $columnGroup->fillVariables();
            $temp2 = $columnGroup->getRelationData('column_array_ids');
            
            $set->column_groups[$i] = (Object)[];
            $set->column_groups[$i]->id = $columnGroup->id;
            $set->column_groups[$i]->name = $columnGroup->name;
            $set->column_groups[$i]->color_class = $columnGroup->getRelationData('color_class_id')->name;
            $set->column_groups[$i]->column_arrays = [];
            
            foreach($temp2 as $j => $columnArray)
            {
                $columnArray->fillVariables();
                
                $temp3 = (Object)[];
                $temp3->id = $columnArray->id;
                $temp3->name = $columnArray->name;
                $temp3->column_array_type = $columnArray->getRelationData('column_array_type_id')->name;
                $temp3->columns = (Object)[];
                
                $temp4 = $this->getAllColumnsFromColumnArray($model, $columnArray->id, $form);
                if($temp4 == NULL) 
                    $temp3->tree =  $columnSetId.':'
                                        .$columnGroup->id.':'
                                        .$columnArray->id;
                else $temp3->columns = $temp4;
                
                $set->column_groups[$i]->column_arrays[$j] = $temp3;
            }
        }
        
        return $set;
    }
    
    public function getColumnsFromColumnSet($columnSet)
    {
        $columns = helper('get_null_object');
        foreach($columnSet->column_groups as $columnGroup)
            foreach($columnGroup->column_arrays as $columnArray)
                foreach(array_keys(get_object_vars($columnArray->columns)) as $columnName)
                    $columns->{$columnName} = $columnArray->columns->{$columnName};
                
        return $columns;
    }
    
    public function getFilteredColumnSet($columnSet, $form = FALSE)
    {
        foreach($columnSet->column_groups as $columnGroupId => $columnGroup)
            foreach($columnGroup->column_arrays as $columnArrayId => $columnArray)
            {
                $columns = $this->getFilteredColumns($columnArray->columns, $form);
                $columnSet->column_groups[$columnGroupId]->column_arrays[$columnArrayId]->columns = $columns;
            }
                
        return $columnSet;
    }
        
        
    
    /****    Common Functions   ****/
    
    private function getAllColumnsFromTable()
    {
        $cacheName = 'tableName:'.$this->getTable().'|allColumsFromDbWithTableAliasAndGuiType';
        $columns = Cache::rememberForever($cacheName, function()
        {   
            $columns = (Object)[];
            $model = new BaseModel($this->getTable());

            foreach($model->getAllColumnsFromDB() as $column)
            {
                $column = get_attr_from_cache('columns', 'name', $column['name'], '*');

                $column->gui_type_name = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
                $column->db_type_name = get_attr_from_cache('column_db_types', 'id', $column->column_db_type_id, 'name');
                $column->table_alias = $this->getTable();

                if(in_array($column->gui_type_name, $this->geometryColumnTypes))
                    $column->srid = helper('get_column_srid', 
                                    [
                                        'table' => $this->getTable(),
                                        'column' => $column->name
                                    ]);
                
                $columns->{$column['name']} = $column;
            }

            return $columns;
        });

        return $columns;
    }
        
    public function getFilteredColumns($columns, $form = FALSE)
    {   
        $keys = array_keys(get_object_vars($columns));        
        $json = json_encode($keys);
        $cacheName = 'tableName:'.$this->getTable().'|columnNames:'.$json.'|form:'.$form.'|filteredColumns';
        
        $return = Cache::rememberForever($cacheName, function() use($columns, $form)
        {   
            $disabledColumns = ['id', 'updated_at', 'created_at', 'user_id', 'own_id'];
            $filteredFields = ['id', 'name', 'display_name', 'table_name', 'gui_type_name', 'column_table_relation_id', 'srid'];
            
            $return = helper('get_null_object');
            foreach($columns as $name => $column)
            {
                if($form)
                    if(in_array($column->name, $disabledColumns))
                        continue;
                
                foreach($filteredFields as $f)
                {
                    if(@is_object($column->{$f}))
                    {
                        dd('object data?');
                        $temp = helper('get_column_data_for_joined_column', $name);
                        $return[$name][$f] = $temp[1];
                    }                    
                    else
                    {
                        if(!isset($return->{$name})) 
                            $return->{$name} = helper('get_null_object');
                            
                        $return->{$name}->{$f} = @$column->{$f};
                    }
                }
                
                if(strlen($column->up_column_id) > 0)
                {
                    $upColumn = $column->getRelationData('up_column_id');
                    $return->{$name}->up_column_name = $upColumn->getRelationData('column_id')->name;
                }
                
                if(strlen($column->column_table_relation_id) > 0)
                {
                    $params = helper('get_null_object');
                    $return->{$name}->relation = ColumnClassificationLibrary::relation(  $this, 
                                                                                                'getRelationTableName', 
                                                                                                $column, 
                                                                                                NULL, 
                                                                                                $params);
                }
            }

            return $return;
        });
        
        return $return;
    }
    
    public function getRelationTableNameForTableIdAndColumnIds($params)
    {
        $table = $params->relation->getRelationData('relation_table_id');
        $sourceColumn = $params->relation->getRelationData('relation_source_column_id');
        $displayColumn = $params->relation->getRelationData('relation_display_column_id');
        
        return 
        [
            'table_name' => $table->name,
            'source_column_name' => $sourceColumn->name,
            'display_column_name' => $displayColumn->name,
        ];
    }
    
    public function getRelationTableNameForRelationSql($params)
    {
        $tableNameWithAlias = explode(' from ', $params->relation->relation_sql)[1];
        $tableName = explode(' as ', $tableNameWithAlias)[0];
        
        return 
        [
            'table_name' => $tableName,
            'source_column_name' => $params->relation->relation_source_column,
            'display_column_name' => $params->relation->relation_display_column
        ];
    }
    
    
    
    public function getRelationTableNameForDataSource($params)
    {
        $dataSource = $params->relation->getRelationData('data_source_id');
        
        return 
        [
            'table_name' => 'data_source',
            'source_column_name' => $dataSource->_source_column,
            'display_column_name' => $dataSource->_display_column
        ];
    }
}