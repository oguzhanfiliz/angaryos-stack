<?php

namespace App\BaseModelTraits;

use \App\BaseModel;
use Cache;

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
        $records = $this->updataDataFromDataSource($records, $params->columns);
        
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
        $params->joins = $params->column_array->getRelationData('join_table_ids');                
        $params->target_table = $params->joins[0]->getRelationData('join_table_id');//users t
        $params->target_column = $params->joins[0]->getRelationData('join_column_id');//department_id c
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
        $cacheName = 'tableName:'.$name.'|tableInfo';        
        $tableInfo = Cache::rememberForever($cacheName, function() use($name)
        {      
            $tableInfo = helper('get_null_object');
            $tableInfo->name = $name;
            $tableInfo->display_name = get_attr_from_cache('tables', 'name', $name, 'display_name');

            return $tableInfo;
        });
        
        return $tableInfo;
    }
    
    public function updataDataFromDataSource($records, $columns)
    {
        if(is_array($records)) $records = (Object)$records;
        
        foreach($columns as $column)
        {
            $guiType = $column->getRelationData('column_gui_type_id');
            if($guiType->name == 'password')
            {
                if(get_class($records) == 'stdClass')
                {
                    $records->{$column->name} = NULL;
                }
                else
                {
                    foreach($records as $i => $record)
                        $records[$i]->{$column->name} = NULL;
                }
            }
                
            
            if(strlen($column->column_table_relation_id) == 0) continue;
            $relation = $column->getRelationData('column_table_relation_id');
            if(strlen($relation->data_source_id) == 0) continue;
            
            
            $dataSource = $relation->getRelationData('data_source_id');
            
            $repository = NULL;
            eval(helper('clear_php_code', $dataSource->php_code));
            
            if(get_class($records) == 'stdClass')
            {
                $records->{$column->name} = $repository->getRecordsForListBySourceData($records->{$column->name});
            }
            else
            {
                foreach($records as $i => $record)
                    $records[$i]->{$column->name} = $repository->getRecordsForListBySourceData($record->{$column->name});
            }
        }

        return $records;
    }
}