<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;
use App\BaseModel;
use Cache;
use DB;

trait BaseModelGetDataColumnTrait 
{    
    private $geometryColumnTypes = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
    
    public function getColumns($model, $tableName, $columnArrayOrSetId)
    {
        $cacheName = 'table:'.$this->getTable().'|type:'.$tableName.'|id:'.$columnArrayOrSetId.'|columnArrayOrSetAndJoins';  
        [$data, $joins]  = Cache::rememberForever($cacheName, function() use($model, $tableName, $columnArrayOrSetId)
        {
            global $pipe;

            switch($tableName)
            {
                case 'column_arrays': 
                    $data = $this->getColumnsByColumnArrayId($model, $columnArrayOrSetId);
                    break;
                case 'column_sets': 
                    $data =  $this->getColumnsByColumnSetId($model, $columnArrayOrSetId); 
                    break;
                default: abort(helper('response_error', 'undefined.type:'.$tableName));  
            }

            if(isset($pipe['joins'])) $joins = $pipe['joins'];
            else $joins = [];

            return [$data, $joins];
        });
        
        foreach($joins as  $join) 
        {
            $join->connection_column_with_alias = helper('reverse_clear_string_for_db', $join->connection_column_with_alias);    
            $this->addJoinForColumnArray($model, $join);
        }

        return $data;
    }

    private function getColumnsByColumnSetId($model, $id, $form = FALSE)
    {
        global $pipe;

        $columns = helper('get_null_object');

        $set = get_attr_from_cache('column_sets', 'id', $id, '*');
        if(strlen($set->column_array_ids) == 0) return $columns;

        $arrays = json_decode($set->column_array_ids);
        foreach($arrays as $arrayId)
        {
            if($arrayId == 0) 
                $temp = $this->getAllColumnsFromTable();
            else
                $temp = $this->getAllColumnsFromColumnArray($model, $arrayId, $form);

            foreach($temp as $columnName => $column)
                $columns->{$columnName} = $column;
        }
        
        return $columns;
    }
        
    private function getColumnsByColumnArrayId($model, $id, $form = FALSE)
    {
        if($id == 0) 
            return $this->getAllColumnsFromTable();
        else
            return $this->getAllColumnsFromColumnArray($model, $id, $form);
    }
    
    private function getAllColumnsFromColumnArray($model, $id, $form = FALSE)
    {
        $columnArray = get_attr_from_cache('column_arrays', 'id', $id, '*');
        
        $type = get_attr_from_cache('column_array_types', 'id', $columnArray->column_array_type_id, 'name');
        switch($type)
        {
            case 'direct_data': return $this->getAllColumnsFromColumnArrayDirectData($model, $columnArray, $form);
            case 'table_from_data': return $this->getAllColumnsFromColumnArrayTableFromData($model, $columnArray);
            default: abort(helper('response_error', 'undefined.column_array.type:'.$type));  
        }
    }
    
    private function getAllColumnsFromColumnArrayDirectData($model, $columnArray, $form = FALSE)
    {
        global $pipe;

        $columns = helper('get_null_object');
        
        if(strlen($columnArray->column_ids) > 0)
            foreach(json_decode($columnArray->column_ids) as $columnId)
            {
                $column = get_attr_from_cache('columns', 'id', $columnId, '*');

                $column->gui_type_name = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
                $column->db_type_name = get_attr_from_cache('column_db_types', 'id', $column->column_db_type_id, 'name');
                $column->table_alias = $this->getTable();

                $columns->{$column->name} = $column;
            }
        
        $type = get_attr_from_cache('column_array_types', 'id', $columnArray->column_array_type_id, 'name');
        if($type == 'direct_data')
            if(strlen($columnArray->join_table_ids) > 0)
                foreach(json_decode($columnArray->join_table_ids) as $joinId)
                {
                    $join = get_attr_from_cache('join_tables', 'id', $joinId, '*');

                    if(!isset($pipe['joins'])) $pipe['joins'] = [];

                    array_push($pipe['joins'], $join);
                }
        
        if($form) return $columns;
        
        $arr = helper('divide_select', $columnArray->join_columns);
        foreach($arr as $c)
        {
            if(strlen(trim($c)) == 0) continue;
            
            $column = (Object)[];
            $temp = helper('get_column_data_for_joined_column', $c);

            $currentColumnModel = get_attr_from_cache('columns', 'name', $temp[1], '*');
            if($currentColumnModel == NULL)
            {
                $currentColumnModel = helper('get_null_object');
                $currentColumnModel->column_gui_type_id = get_attr_from_cache('column_gui_types', 'name', 'string', 'id');
                $currentColumnModel->column_db_type_id = get_attr_from_cache('column_gui_types', 'name', 'string', 'id');
                $currentColumnModel->display_name = ucfirst($temp[1]);
            }
            
            if(strstr($currentColumnModel->column_gui_type_id, 'select')) 
                $currentColumnModel->column_gui_type_id = 'string';
                //https://192.168.10.185/api/v1/sd8ymkQNek2q7YCOd1/tables/test/getSelectColumnData/table_id?search=***
            
            $column->id = -1;
            $column->name = $temp[1];
            $column->display_name = $currentColumnModel->display_name;
            $column->column_table_relation_id = NULL;
            $column->gui_type_name = get_attr_from_cache('column_gui_types', 'id', $currentColumnModel->column_gui_type_id, 'name');
            $column->column_gui_type_id = $currentColumnModel->column_gui_type_id;
            $column->db_type_name = get_attr_from_cache('column_db_types', 'id', $currentColumnModel->column_db_type_id, 'name');
            $column->column_db_type_id = $currentColumnModel->column_db_type_id;
            $column->table_alias = '';
            $column->table_name = '';
            $column->up_column_id = NULL;
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
        $cacheName = 'table:'.$this->getTable().'|type:column_sets|id:'.$columnSetId.'|columnSetObjectAndJoins';        
        [$data, $joins]  = Cache::rememberForever($cacheName, function() use($model, $columnSetId, $form)
        {
            global $pipe;

            if($columnSetId == 0) 
                $data = $this->getColumnSetDefault();
            else
                $data = $this->getColumnSetByColumnSetId($model, $columnSetId, $form);

            if(isset($pipe['joins'])) $joins = $pipe['joins'];
            else $joins = [];

            return [$data, $joins];
        });
        
        foreach($joins as  $join) $this->addJoinForColumnArray($model, $join);

        return $data;
    }
    
    private function getColumnSetDefault()
    {
        $set = (Object)[];
        $set->name = '';
        $set->column_set_type = 'none';
        $set->column_arrays = [];
        
        $set->column_arrays[0] = (Object)[];
        $set->column_arrays[0]->id = 0;
        $set->column_arrays[0]->name_basic = '';
        $set->column_arrays[0]->column_array_type = 'direct_data';
        $set->column_arrays[0]->columns = $this->getAllColumnsFromTable();
        
        /*$set = (Object)[];
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
        $set->column_groups[0]->column_arrays[0]->columns = $this->getAllColumnsFromTable();*/
        
        return $set;
    }
    
    private function getColumnSetByColumnSetId($model, $columnSetId, $form)
    {
        $columnSet = new BaseModel('column_sets');
        $columnSet = $columnSet->find($columnSetId);
        
        $temp = $columnSet->getRelationData('column_array_ids');
        
        $set = (Object)[];
        $set->name_basic = $columnSet->name_basic;
        $set->column_set_type = $columnSet->getRelationData('column_set_type_id')->name;
        $set->column_arrays = [];
            
        foreach($temp as $j => $columnArray)
        {
            $columnArray->fillVariables();

            $temp2 = (Object)[];
            $temp2->id = $columnArray->id;
            $temp2->name_basic = $columnArray->name_basic;
            $temp2->column_array_type = $columnArray->getRelationData('column_array_type_id')->name;
            $temp2->columns = (Object)[];

            $temp3 = $this->getAllColumnsFromColumnArray($model, $columnArray->id, $form);
            if($temp3 == NULL) 
                $temp2->tree =  $columnSetId.':'
                                    .$columnArray->id;
            else $temp2->columns = $temp3;

            $set->column_arrays[$j] = $temp2;
        }
        
        return $set;
    }
    
    public function getColumnsFromColumnSet($columnSet)
    {
        $columns = helper('get_null_object');
        foreach($columnSet->column_arrays as $columnArray)
            foreach(array_keys(get_object_vars($columnArray->columns)) as $columnName)
                $columns->{$columnName} = $columnArray->columns->{$columnName};
                
        return $columns;
        
        /*$columns = helper('get_null_object');
        foreach($columnSet->column_groups as $columnGroup)
            foreach($columnGroup->column_arrays as $columnArray)
                foreach(array_keys(get_object_vars($columnArray->columns)) as $columnName)
                    $columns->{$columnName} = $columnArray->columns->{$columnName};
                
        return $columns;*/
    }
    
    public function getFilteredColumnSet($columnSet, $form = FALSE)
    {
        foreach($columnSet->column_arrays as $columnArrayId => $columnArray)
        {
            $columns = $this->getFilteredColumns($columnArray->columns, $form);
            $columnSet->column_arrays[$columnArrayId]->columns = $columns;
        }
                
        return $columnSet;
        /*foreach($columnSet->column_groups as $columnGroupId => $columnGroup)
            foreach($columnGroup->column_arrays as $columnArrayId => $columnArray)
            {
                $columns = $this->getFilteredColumns($columnArray->columns, $form);
                $columnSet->column_groups[$columnGroupId]->column_arrays[$columnArrayId]->columns = $columns;
            }
                
        return $columnSet;*/
    }
        
        
    
    /****    Common Functions   ****/
    
    private function getAllColumnsFromTable()
    {
        $cacheName = 'tableName:'.$this->getTable().'|allColumsFromDbWithTableAliasAndGuiType';
        $columns = Cache::rememberForever($cacheName, function()
        {   
            $tableName = $this->getTable();
            $archiveTable = (bool)strstr($tableName, '_archive');
            $tableName = str_replace('_archive', '', $tableName);
            
            $json = get_attr_from_cache('tables', 'name', $tableName, 'column_ids');
            
            $columnsSort = json_decode($json);
            if($archiveTable) array_push($columnsSort, get_attr_from_cache ('columns', 'name', 'record_id', 'id'));
            
            $model = new BaseModel($this->getTable());
            $allColumnsFromDB = $model->getAllColumnsFromDB();
            
            $columns = (Object)[];
            foreach($columnsSort as $columnId)
            {
                $column = get_attr_from_cache('columns', 'id', $columnId, '*');
                if(substr($column->name, 0, 8) == 'deleted_') continue;
                
                $column->gui_type_name = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
                $column->db_type_name = get_attr_from_cache('column_db_types', 'id', $column->column_db_type_id, 'name');
                $column->table_alias = $this->getTable();

                if(in_array($column->gui_type_name, $this->geometryColumnTypes))
                    $column->srid = helper('get_column_srid', 
                                    [
                                        'table' => $this->getTable(),
                                        'column' => $column->name
                                    ]);
                
                $columns->{$column->name} = $column;
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
            $filteredFields = ['id', 'name', 'display_name', 'table_name', 'gui_type_name', 'column_table_relation_id', 'srid', 'default'];
            
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
                        dd('getFilteredColumns object data?');
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
                    $upColumnId = get_attr_from_cache('up_columns', 'id', $column->up_column_id, 'column_id');
                    $upColumnName = get_attr_from_cache('columns', 'id', $upColumnId, 'name');
                    
                    $return->{$name}->up_column_name = $upColumnName;
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
                
                $return->{$name}->e_sign = FALSE;
                if(strlen(@$column->e_sign_pattern_c) > 0) $return->{$name}->e_sign = TRUE;
                
                $return->{$name}->column_info = $column->column_info;
            }

            return $return;
        });
        
        return $return;
    }
    
    public function getRelationTableNameForJoinTableIds($params)
    {
        $tableName = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, 'name');
        
        return 
        [
            'table_name' => $tableName,
            'source_column_name' => $params->relation->relation_source_column,
            'display_column_name' => $params->relation->relation_display_column,
        ];
    }
    
    public function getRelationTableNameForTableIdAndColumnIds($params)
    {
        $tableName = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, 'name');
        $sourceColumnName = get_attr_from_cache('columns', 'id', $params->relation->relation_source_column_id, 'name');
        $displayColumnName = get_attr_from_cache('columns', 'id', $params->relation->relation_display_column_id, 'name');
        
        return 
        [
            'table_name' => $tableName,
            'source_column_name' => $sourceColumnName,
            'display_column_name' => $displayColumnName,
        ];
    }

    public function getRelationTableNameForTableIdAndColumnNames($params)
    {
        $tableName = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, 'name');
        $sourceColumnName = $params->relation->relation_source_column;
        $displayColumnName = $params->relation->relation_display_column;
        
        return 
        [
            'table_name' => $tableName,
            'source_column_name' => $sourceColumnName,
            'display_column_name' => $displayColumnName,
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
        $dataSource = get_attr_from_cache('column_data_sources', 'id', $params->relation->column_data_source_id, '*');
        
        return 
        [
            'table_name' => 'data_source',
            'source_column_name' => $dataSource->id,
            'display_column_name' => $dataSource->name
        ];
    }
}