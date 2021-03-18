<?php

namespace App\BaseModelTraits;

use \App\BaseModel;
use Cache;
use DB;

trait BaseModelGetDataTrait 
{   
    use BaseModelGetDataColumnTrait;
    use BaseModelGetDataSelectTrait;
    use BaseModelGetDataWhereTrait;
    use BaseModelGetDataSortTrait;
    use BaseModelGetDataJoinTrait;
    use BaseModelGetDataCollectiveInfoTrait;
    use BaseModelGetDataGuiTriggersTrait;
    use BaseModelGetDataFilterTrait;
    
    
    
    /****    Relation Data For Info    ****/
    
    public function getRelationTableDataForInfo($params)
    {   
        $params = $this->getModelForRelationData($params);
        
        $count = $params->model->count($params->table_name.'.id');
        
        $tableInfo = $this->getTableInfo($params->table_name);
        
        $columns = $this->getFilteredColumns($params->columns);
        
        $collectiveInfos = $this->getCollectiveInfos($params->model, $params->columns);
        
        $params->model->limit($params->limit);
        $params->model->offset($params->limit * ($params->page - 1));
        
        $records = $params->model->get();
        
        $records = $this->updateRecordsDataForResponse($records, $params->columns);
        
        $records = $this->updateRecordsESignDataForResponse($records, $tableInfo, $params->columns);
        
        return 
        [
            'table_info' => $tableInfo,
            'records' => $records,
            'collectiveInfos' => $collectiveInfos,
            'columns' => $columns,
            'query_columns' => $columns,
            'pages' => (int)ceil($count / $params->limit),
            'all_records_count' => $count
        ];
    }
    
    public function getModelForRelationData($params)
    {
        global $pipe;
        
        $temp = json_decode($params->column_array->join_table_ids);
        $params->joins = [];
        foreach($temp as $joinId)
            array_push ($params->joins, get_attr_from_cache('join_tables', 'id', $joinId, '*'));
                      
        $params->target_table = get_attr_from_cache('tables', 'id', $params->joins[0]->join_table_id, '*');//users t
        $params->target_column = get_attr_from_cache('columns', 'id', $params->joins[0]->join_column_id, '*');//department_id c
        $params->target_column->table_alias = $params->target_table->name;
                
        $pipe['table'] = $params->target_table->name;
        
        $params->record = new BaseModel($params->target_table->name);
        $params->record->fillVariables();
        
        $params->model = $params->record->getQuery();
        $params->table_name = $params->record->getTable();
        
        $params->columns = $params->record->getColumns($params->model, 'column_arrays', $params->column_array_id);
        
        $params->record->addJoinsWithColumns($params->model, $params->columns);
        $params->record->addSorts($params->model, $params->columns, $params->sorts);
        $params->record->addWheres($params->model, $params->columns, $params->filters);
        $params->record->addSelects($params->model, $params->columns);
        $params->record->addFilters($params->model, $params->table_name, 'list');
        
        $relationFilter = $this->getFilterForRelationData($params); 
        $params->record->addWhere($params->model, $params->target_column, $relationFilter);
        
        unset($params->joins[0]);
        foreach($params->joins as $join)
            $params->record->addJoinForColumnArray($params->model, $join);
        
        $params->model->addSelect($params->table_name.'.id');
        $params->model->groupBy($params->table_name.'.id');
        
        return $params;
    }
    
    
        
    /****    Common Functions    ****/
    
    public function getTableInfo($name)
    {
        if(substr($name, -8, 8) == '_archive') $name = substr($name, 0, -8);

        $cacheName = 'tableName:'.$name.'|tableInfo'; 
        $tableInfo = Cache::rememberForever($cacheName, function() use($name)
        {      
            $table = get_attr_from_cache('tables', 'name', $name, '*');
            
            $tableInfo = helper('get_null_object');
            $tableInfo->name = $name;
            $tableInfo->display_name = $table->display_name;
            $tableInfo->up_table = false;
            
            $control = DB::table('sub_tables')
                            ->whereRaw('table_ids @> \''.$table->id.'\'::jsonb or table_ids @> \'"'.$table->id.'"\'::jsonb')
                            ->first();
            
            if($control) $tableInfo->up_table = TRUE;
            
            $tableInfo->e_sign = FALSE;
            if(strlen($table->e_sign_pattern_t) > 0) $tableInfo->e_sign = TRUE;

            return $tableInfo;
        });
        
        return $tableInfo;
    }
    
    private function updateRecordsDataForResponseGuiTypeRichText($data)
    {
        return helper('reverse_clear_string_for_db', $data);
    }
    
    private function updateRecordsDataForResponseFromDataSource($record, $column, $relation)
    {
        $dataSource = get_attr_from_cache('column_data_sources', 'id', $relation->column_data_source_id, '*');
        $repository = NULL;
        eval(helper('clear_php_code', $dataSource->php_code));

        return $repository->getRecordsForListBySourceData($record, $column);
    }
    
    private function updateRecordsDataForResponseSingleData($record, $column)
    {
        if(strlen($column->column_table_relation_id) > 0) 
        {
            $relation = get_attr_from_cache('column_table_relations', 'id', $column->column_table_relation_id, '*');
            
            if(strlen($relation->column_data_source_id) > 0)
                return $this->updateRecordsDataForResponseFromDataSource($record, $column, $relation);
        }
        
        $data = $record->{$column->name};
        $guiTypeName = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
        switch ($guiTypeName) 
        {
            case 'password': return NULL;
            case 'rich_text': return $this->updateRecordsDataForResponseGuiTypeRichText($data);
            default: return $data;
        }
    }
    
    public function updateRecordsESignDataForResponse($records, $tableInfo, $columns)
    {
        $ids = [];
        foreach($records as $record) array_push($ids, $record->id);
        
        $temp = DB::table('e_signs')
                        ->where('table_id', get_attr_from_cache('tables', 'name', $tableInfo->name, 'id'))
                        ->whereIn('source_record_id', $ids)
                        ->orderBy('id')
                        ->get();
        
        $eSings = [];
        foreach($temp as $sign)
        {
            $control = strlen($sign->signed_at) > 0;
            
            if(!isset($eSings[$sign->source_record_id])) $eSings[$sign->source_record_id] = [];
            
            if($sign->column_id == NULL) 
                $eSings[$sign->source_record_id][0] = $control;
            else 
                $eSings[$sign->source_record_id][$sign->column_id] = $control;
        }
        
        foreach($records as $i => $record)
        {
            $records[$i]->_e_sings = [];
            
            if($tableInfo->e_sign) $records[$i]->_e_sings['0'] = (bool)@$eSings[$record->id][0];
            else $records[$i]->_e_sings['0'] = NULL;
            
            foreach($columns as $column)
                if(strlen(@$column->e_sign_pattern_c) > 0)
                    $records[$i]->_e_sings[$column->name] = (bool)@$eSings[$record->id][$column->id];
                else 
                    $records[$i]->_e_sings[$column->name] = NULL; 
        }
        
        return $records;
    }
    
    private function UpdateRecordsDataForResponseReverseClearStringForDB($records, $columns)
    {
        $single = FALSE;
        if(get_class($records) == 'stdClass')
        {
            $records = [$records];
            $single = TRUE;
        }

        foreach($records as $i => $record)
            foreach($columns as $column)
                $records[$i]->{$column->name} = helper('reverse_clear_string_for_db', $record->{$column->name});
           
        if($single) $records = $records[0];

        return $records;
    }
    
    public function updateRecordsDataForResponse($records, $columns)
    {
        if(is_array($records)) $records = (Object)$records;
        
        $records = $this->UpdateRecordsDataForResponseReverseClearStringForDB($records, $columns);
        
        foreach($columns as $column)
        {
            if(!isset($column->column_gui_type_id)) 
                dd('updateRecordsDataForResponse');
            
            $records = $this->UpdateGeoColumnsDataForResponse($records, $column);
            
            if(get_class($records) == 'stdClass')
                $records->{$column->name} = $this->updateRecordsDataForResponseSingleData($records, $column);
            else
                foreach($records as $i => $record)
                    $records[$i]->{$column->name} = $this->updateRecordsDataForResponseSingleData($records[$i], $column);
        }

        return $records;
    }
    
    public function  UpdateGeoColumnsDataForResponse($records, $column)
    {
        $geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
        
        $columnGuiTypeName = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
        if(!in_array($columnGuiTypeName, $geoColumns)) return $records;
                    
        if(get_class($records) == 'stdClass')
            $records->{$column->name} = $this->GetWKTFromPGDBString($records->{$column->name});
        else
            foreach($records as $i => $record)
                $records[$i]->{$column->name} = $this->GetWKTFromPGDBString($records[$i]->{$column->name});
                
        return $records;
    }
    
    public function GetWKTFromPGDBString($str)
    {
        if($str == NULL) return NULL;
        if(strlen($str) == 0) return "";
        
        return DB::select('select st_astext(\''.$str.'\') as data')[0]->data;
    }
}