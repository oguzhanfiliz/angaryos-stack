<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;

use DB;

trait BaseModelGetDataSelectTrait 
{    
    public function addSelects($model, $columns)
    {
        foreach($columns as $column)
            $this->addSelect($model, $column);
    }
        
    private function addSelect($model, $column)
    {
        $params = helper('get_null_object');
        $params->model = $model;
        $params->column = $column;
        
        ColumnClassificationLibrary::relation($this, __FUNCTION__, $column, NULL, $params);
    }
    
    public function addSelectForBasicColumn($params)
    {
        $alias = $params->column->table_alias;
        if(strlen($params->column->table_alias) == 0) $alias = $params->table_name;
        
        $params->model->addSelect($alias.'.'.$params->column->name);
    }
    
    public function addSelectForRelationSql($params)
    {
        $params->table_alias = $params->column->name.'___sql_relation'.$params->relation->id;
        $params->relation_source_column_with_alias = $params->table_alias.'.'.$params->relation->relation_source_column;
        $params->column_with_alias = $params->table_alias.'.'.$params->relation->relation_display_column;
        
        $this->addSelectForColumnsDBTypesStatus($params);
    }
    
    public function addSelectForJoinedColumn($params)
    {
        $params->model->addSelect(DB::raw($params->column->select_raw));
    }
    
    public function addSelectForTableIdAndColumnIds($params)
    {
        $table = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, '*');
        $source = get_attr_from_cache('columns', 'id', $params->relation->relation_source_column_id, '*');
        $display = get_attr_from_cache('columns', 'id', $params->relation->relation_display_column_id, '*');        
        
        $params->table_alias  = $params->column->name.'___'.$table->name.$params->relation->id;
        $params->relation_source_column_with_alias = $params->table_alias.'.'.$source->name;
        $params->column_with_alias  = $params->table_alias.'.'.$display->name;
        
        $this->addSelectForColumnsDBTypesStatus($params);
    }
    
    public function addSelectForDataSource($params)
    {
        $alias = $params->column->table_alias;
        if(strlen($params->column->table_alias) == 0) $alias = $params->table_name;
        
        $params->model->addSelect($alias.'.'.$params->column->name);
    }
    
    
    
    /****    Common Functions    ****/
    
    public function addSelectForColumnsDBTypesStatus($params)
    {
        ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                        __FUNCTION__, 
                                                        $params->column, 
                                                        NULL, 
                                                        $params);
    }
    
    public function addSelectForColumnsDBTypesStatusForOneToOne($params)
    {
        $temp = "string_agg($params->column_with_alias, ',')";
        $temp = "split_part($temp, ',', 1)";
        $temp .= ' as ' . $params->column->name;
        
        $params->model->addSelect(DB::raw($temp));
    }
    
    public function addSelectForColumnsDBTypesStatusForOneToMany($params)
    {
        //{"1": {"source": 1, "display": "ID"}, "2": {"source": 3, "display": "Ad"}}
        
        $temp = ' \'"\' || ' .$params->table_alias.'_lateral.ordinality::text || \'": {"source": "\' || '
                .$params->relation_source_column_with_alias.' || \'", "display": "\' || ' 
                .$params->column_with_alias . ' || \'"}\'';
        $temp = "'{' || string_agg(distinct ($temp), ', ') || '}'";
        $temp .= ' as ' . $params->column->name;
        
        $params->model->addSelect(DB::raw($temp));
    }
    
    
    
    
    
    
    
    
    
    
   
    
    
    
    public function add_select_for_join_table_ids($params)
    {
        dd('add_select_for_join_table_ids');
        //daha önce joined tabledan ekleme yapıldı add_select_for_joined_column
    }
    
    
    
    
    
    
}