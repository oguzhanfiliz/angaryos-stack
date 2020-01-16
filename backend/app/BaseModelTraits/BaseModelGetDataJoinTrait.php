<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;
use DB;

trait BaseModelGetDataJoinTrait 
{    
    /****    Join For Selected Columns    ****/
    
    public function addJoinsWithColumns($model, $columns)
    {
        foreach($columns as $column)
            if(strlen($column->column_table_relation_id) > 0)
                $this->addJoinWithColumn($model, $column);
    }
    
    private function addJoinWithColumn($model, $column)
    {            
        $params = helper('get_null_object');
        $params->model = $model;
        $params->column = $column;
        
        ColumnClassificationLibrary::relation(  $this, 
                                                __FUNCTION__, 
                                                $column, 
                                                NULL, 
                                                $params);
    }
    
    public function addJoinWithColumnForRelationSql($params)
    {
        $params->tableAlias = $params->column->name.'___sql_relation'.$params->relation->id;
        $params->joinTable = '('.$params->relation->relation_sql.') as '.$params->tableAlias;
        
        ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                        __FUNCTION__, 
                                                        $params->column, 
                                                        NULL/*$params->targetColumn*/, 
                                                        $params);
    }
    
    public function addJoinWithColumnForRelationSqlForOneToOne($params)
    {
        $params->model->leftJoin(DB::raw($params->joinTable), 
        function($join) use($params)
        {
           $join->on(
                   $this->getTable().'.'.$params->column->name,
                   '=', 
                   $params->tableAlias.'.'.$params->relation->relation_source_column);
        });
    }
    
    public function addJoinWithColumnForTableIdAndColumnIds($params)
    {
        $params->join_table = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, '*');
        $params->join_source = get_attr_from_cache('columns', 'id', $params->relation->relation_source_column_id, '*');
        
        $params->join_table_alias = $params->column->name.'___'.$params->join_table->name.$params->relation->id;
        
        if(isset($params->column->join_table_alias)) 
            $params->join_table_alias = $params->column->join_table_alias;
        
        ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                        __FUNCTION__, 
                                                        $params->column, 
                                                        $params->relation, 
                                                        $params);
    }
    
    public function addJoinWithColumnForTableIdAndColumnIdsForOneToOne($params)
    {
        if(isset($params->column->join_table_alias)) 
            $params->join_table_alias = $params->column->join_table_alias;
        
        $params->model->leftJoin(
                $params->join_table->name . ' as ' . $params->join_table_alias, 
                $params->join_table_alias.'.'.$params->join_source->name,
                '=',
                $this->getTable().'.'.$params->column->name);
    }
                    
    public function addJoinWithColumnForTableIdAndColumnIdsForOneToMany($params)
    {
        $lateral = 'lateral jsonb_array_elements('
                .$this->getTable().'.'.$params->column->name.') with ordinality ' 
                .' as '.$params->join_table_alias.'_lateral';
        $params->model->leftJoin(DB::raw($lateral), DB::raw('true'), '=', DB::raw('true'));
        
        $params->model->leftJoin(
                $params->join_table->name . ' as ' . $params->join_table_alias, 
                DB::raw('('.$params->join_table_alias.'_lateral.value->>0)::bigint'),
                '=',
                $params->join_table_alias.'.'.$params->join_source->name);
    }
    
    public function addJoinWithColumnForDataSource($params) { }
    
    
    
    /****    Join For Column Array's Join Tables    ****/
    
    private function addJoinForColumnArray($model, $join)
    {
        $params = helper('get_null_object');
        
        $params->join = $join;
        $params->model = $model;
        
        $params->realtion_table = $join->getRelationData('join_table_id');
        $params->realtion_table_name = $params->realtion_table->name;
        if(strlen($join->join_table_alias) > 0)
            $params->realtion_table_name .= ' as ' . $join->join_table_alias;

        $params->realtion_column = $join->getRelationData('join_column_id');
        $params->realtion_column_name = $params->realtion_column->name;
        if(strlen($join->join_table_alias) > 0)
            $params->realtion_column_name = $join->join_table_alias.'.'.$params->realtion_column_name;
        
        $columnName = explode('.', $join->connection_column_with_alias);
        $columnName = last($columnName);
        $params->column = get_attr_from_cache('columns', 'name', $columnName, '*');
        
        $params->relation = $join->getRelationData('join_column_id');
        
        return ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                                __FUNCTION__, 
                                                                $params->column, 
                                                                $params->realtion_column, 
                                                                $params);
    }
    
    public function addJoinForColumnArrayForOneToOne($params)
    {
        return $params->model->leftJoin(
                $params->realtion_table_name, 
                $params->realtion_column_name, 
                $params->join->join_connection_type, 
                $params->join->connection_column_with_alias);
    }
    
    
    
    
    
    public function add_join_for_column_array_for_many_to_one($params)
    {
        $params->model->leftJoin(
                $params->realtion_table_name, 
                $params->realtion_column_name, 
                '@>', 
                $params->join->connection_column_with_alias);
    }
    
    public function add_join_for_column_array_for_one_to_many($params)
    {
        $params->model->leftJoin(
                $params->realtion_table_name, 
                $params->join->connection_column_with_alias, 
                '@>', 
                $params->realtion_column_name);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function add_join_for_relation_sql_for_one_to_many($params)
    {
        dd('many için yaz');
        $params->model->leftJoin(DB::raw($params->join_table), 
        function($join) use($params)
        {
           $join->on(
                   $params->record->getTable().'.'.$params->column->name,
                   '=', 
                   $params->table_alias.'.'.$params->relation->relation_source_column);
        });
    }
    
    
    
    
    
    
    
    
    
    
    
    public function add_join_for_with_join_table_ids($params)
    {
        dd('add_join_for_with_join_table_ids');
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}