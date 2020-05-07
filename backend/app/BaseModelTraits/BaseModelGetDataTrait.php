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
        $temp = json_decode($params->column_array->join_table_ids);
        $params->joins = [];
        foreach($temp as $joinId)
            array_push ($params->joins, get_attr_from_cache('join_tables', 'id', $joinId, '*'));
                      
        $params->target_table = get_attr_from_cache('tables', 'id', $params->joins[0]->join_table_id, '*');//users t
        $params->target_column = get_attr_from_cache('columns', 'id', $params->joins[0]->join_column_id, '*');//department_id c
        $params->target_column->table_alias = $params->target_table->name;
        
        $params->record = new BaseModel($params->target_table->name);
        $params->record->fillVariables();
        
        $params->model = $params->record->getQuery();
        $params->table_name = $params->record->getTable();
        
        $params->columns = $params->record->getColumns($params->model, 'column_arrays', $params->column_array_id);
        
        $params->record->addJoinsWithColumns($params->model, $params->columns);
        $params->record->addSorts($params->model, $params->columns, $params->sorts);
        $params->record->addWheres($params->model, $params->columns, $params->filters);
        $params->record->addSelects($params->model, $params->columns);
        $params->record->addFilters($params->model, $params->table_name);
        
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
            $tableInfo = helper('get_null_object');
            $tableInfo->name = $name;
            $tableInfo->display_name = get_attr_from_cache('tables', 'name', $name, 'display_name');
            $tableInfo->up_table = false;
            
            $tableId = get_attr_from_cache('tables', 'name', $name, 'id');
            
            $control = DB::table('sub_tables')
                            ->whereRaw('table_ids @> \''.$tableId.'\'::jsonb or table_ids @> \'"'.$tableId.'"\'::jsonb')
                            ->first();
            
            if($control) $tableInfo->up_table = TRUE;

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
    
    public function updateRecordsDataForResponse($records, $columns)
    {
        if(is_array($records)) $records = (Object)$records;
        
        foreach($columns as $column)
        {
            if(!isset($column->column_gui_type_id)) 
                dd('updateRecordsDataForResponse');
            
            
            if(get_class($records) == 'stdClass')
                $records->{$column->name} = $this->updateRecordsDataForResponseSingleData($records, $column);
            else
                foreach($records as $i => $record)
                    $records[$i]->{$column->name} = $this->updateRecordsDataForResponseSingleData($records[$i], $column);
        }

        return $records;
    }
}