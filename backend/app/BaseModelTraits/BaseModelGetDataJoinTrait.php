<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;
use DB;

trait BaseModelGetDataJoinTrait 
{    
    /****    Join For Selected Columns    ****/
    
    public function addJoinsWithColumns($model, $columns, $disableFirstJoin = FALSE)
    {
        foreach($columns as $column)
            if(strlen($column->column_table_relation_id) > 0)
                $this->addJoinWithColumn($model, $column, $disableFirstJoin);
    }
    
    private function addJoinWithColumn($model, $column, $disableFirstJoin = FALSE)
    {            
        $params = helper('get_null_object');
        $params->model = $model;
        $params->column = $column;
        $params->disable_first_join = $disableFirstJoin;
        
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
    
    public function addJoinWithColumnForJoinTableIds($params)
    {
        global $pipe;

        $joinIds = json_decode($params->relation->join_table_ids);
        
        if(@$params->disable_first_join) unset($joinIds[0]);
        
        $i = 0;
        foreach($joinIds as $joinId)
        {
            //if(in_array($join->id, $pipe['addedJoins'])) continue;
            //array_push($pipe['addedJoins'], $join->id);

            $params->joinIndex = $i++;

            $params->join = get_attr_from_cache('join_tables', 'id', $joinId, '*');
            
            $params->joinTable = get_attr_from_cache('tables', 'id', $params->join->join_table_id, '*');
            $params->joinColumn = get_attr_from_cache('columns', 'id', $params->join->join_column_id, '*');
            
            $columnName = explode('.', $params->join->connection_column_with_alias);
            $columnName = last($columnName);
            $params->column = get_attr_from_cache('columns', 'name', $columnName, '*');
            
            ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                        __FUNCTION__, 
                                                        $params->column, 
                                                        $params->joinColumn, 
                                                        $params);
        }
    }

    public function addJoinWithColumnForTableIdAndColumnNames($params)
    {
        $params->join_table = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, '*');
        $params->join_source = $params->relation->relation_source_column;
        
        $params->join_table_alias = $params->column->name.'___'.$params->join_table->name.$params->relation->id;
        
        if(isset($params->column->join_table_alias)) 
            $params->join_table_alias = $params->column->join_table_alias;
        
        ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                        __FUNCTION__, 
                                                        $params->column, 
                                                        $params->relation, 
                                                        $params);
    }

    public function addJoinWithColumnForTableIdAndColumnNamesForOneToOne($params)
    {
        if(isset($params->column->join_table_alias)) 
            $params->join_table_alias = $params->column->join_table_alias;
			
		$params->join_source = helper('reverse_clear_string_for_db', $params->join_source);
		if($params->join_source[0] == '"' && $params->join_source[strlen($params->join_source)-1] == '"') $params->join_source = substr($params->join_source, 1, -1);
        
        $params->model->leftJoin(
                $params->join_table->name . ' as ' . $params->join_table_alias, 
                $params->join_table_alias.'.'.$params->join_source,
                '=',
                $this->getTable().'.'.$params->column->name);
    }
                    
    public function addJoinWithColumnForTableIdAndColumnNamesForOneToMany($params)
    {
        $lateral = 'lateral jsonb_array_elements('
                .$this->getTable().'.'.$params->column->name.') with ordinality ' 
                .' as '.$params->join_table_alias.'_lateral';
        $params->model->leftJoin(DB::raw($lateral), DB::raw('true'), '=', DB::raw('true'));
		
		$params->join_source = helper('reverse_clear_string_for_db', $params->join_source);
		if($params->join_source[0] == '"' && $params->join_source[strlen($params->join_source)-1] == '"') $params->join_source = substr($params->join_source, 1, -1);
        
        $params->model->leftJoin(
                $params->join_table->name . ' as ' . $params->join_table_alias, 
                DB::raw('('.$params->join_table_alias.'_lateral.value->>0)::bigint'),
                '=',
                $params->join_table_alias.'.'.$params->join_source);
    }
    
    public function addJoinWithColumnForJoinTableIdsForOneToOne($params)
    {
        global $pipe;
        
        $temp = substr($params->join->connection_column_with_alias, 0, strlen($pipe['table'])+1);
        if(
            $temp == $pipe['table'].'.' 
            && 
            (
                \Request::segment(7) == 'archive'
                ||
                \Request::segment(6) == 'deleted'
            )
        )
        {
            $params->join->connection_column_with_alias = str_replace(
                                                                    $pipe['table'].'.',
                                                                    $pipe['table'].'_archive.',
                                                                    $params->join->connection_column_with_alias);
        }
        
        if(!strstr($params->join->connection_column_with_alias, '.')) 
            $params->join->connection_column_with_alias = $this->getTable().'.'.$params->join->connection_column_with_alias;
        
        if(@$params->disable_first_join)
            if($params->joinIndex == 0)
                if(strstr($params->join->connection_column_with_alias, '.'))
                    $params->join->connection_column_with_alias = $this->getTable().'.'.explode('.', $params->join->connection_column_with_alias)[1];

        $params->model->leftJoin($params->joinTable->name . ' as ' . $params->join->join_table_alias, 
        function($join) use($params)
        {
           $join->on(
                   $params->join->join_table_alias.'.'.$params->joinColumn->name,
                   '=', 
                   $params->join->connection_column_with_alias);
        });
    }
    
    public function addJoinWithColumnForJoinTableIdsForOneToMany($params)
    {
        $colName = $params->join->connection_column_with_alias;
        if(!strstr($colName, '.'))
            $colName = $this->getTable().'.'.$colName;

        $lateral = 'lateral
         jsonb_array_elements('
                .$colName.') with ordinality ' 
                .' as '.$params->join->join_table_alias.'_lateral';
        $params->model->leftJoin(DB::raw($lateral), DB::raw('true'), '=', DB::raw('true'));
        
        $params->model->leftJoin($params->joinTable->name . ' as ' . $params->join->join_table_alias,
                DB::raw('('.$params->join->join_table_alias.'_lateral.value->>0)::bigint'),
                '=',
                $params->join->join_table_alias.'.'.$params->joinColumn->name);
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
    
    public function addJoinWithColumnForRelationSqlForOneToMany($params)
    {
        $lateral = 'lateral jsonb_array_elements('
                .$this->getTable().'.'.$params->column->name.') with ordinality ' 
                .' as '.$params->tableAlias.'_lateral';
        $params->model->leftJoin(DB::raw($lateral), DB::raw('true'), '=', DB::raw('true'));
        
        $params->model->leftJoin(DB::raw($params->joinTable), 
                DB::raw('('.$params->tableAlias.'_lateral.value->>0)::bigint'),
                '=',
                $params->tableAlias.'.'.$params->relation->relation_source_column);
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
    
    public function addJoinForColumnArray($model, $join)
    {
        global $pipe;

        //if(isset($pipe['AddedJoin.'.$join->id])) return;
        //$pipe['AddedJoin.'.$join->id] = TRUE;

        $params = helper('get_null_object');
        
        $params->join = $join;
        $params->model = $model;
        
        $params->realtion_table = get_attr_from_cache('tables', 'id', $join->join_table_id, '*');
                
        $params->realtion_table_name = $params->realtion_table->name;
        if(strlen($join->join_table_alias) > 0)
            $params->realtion_table_name .= ' as ' . $join->join_table_alias;

        $params->realtion_column = get_attr_from_cache('columns', 'id', $join->join_column_id, '*');
        $params->realtion_column_name = $params->realtion_column->name;
        if(strlen($join->join_table_alias) > 0)
            $params->realtion_column_name = $join->join_table_alias.'.'.$params->realtion_column_name;
        
        $join->connection_column_with_alias = helper('reverse_clear_string_for_db', $join->connection_column_with_alias);

        $columnName = explode('.', $join->connection_column_with_alias);
        $columnName = last($columnName);
        $columnName = trim($columnName, '"');
        $params->column = get_attr_from_cache('columns', 'name', $columnName, '*');
        
        $params->relation = get_attr_from_cache('columns', 'id', $join->join_column_id, '*');
        
        if(!$params->column) return $this->addJoinForColumnArrayForOneToOne($params);
        
        return ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                                __FUNCTION__, 
                                                                $params->column, 
                                                                $params->realtion_column, 
                                                                $params);
    }
    
    public function addJoinForColumnArrayForOneToOne($params)
    {
        global $pipe;
        $temp = ' '.$params->join->connection_column_with_alias;
        $temp = str_replace(' "', $pipe['table'].'."', $temp);

        return $params->model->leftJoin(
                $params->realtion_table_name, 
                $params->realtion_column_name, 
                '=', 
                DB::raw($temp));
    } 

    public function addJoinForColumnArrayForOneToMany($params)
    {
        global $pipe;
        $temp = ' '.$params->join->connection_column_with_alias;
        $temp = str_replace(' "', $pipe['table'].'."', $temp);

        
        $alias = explode(' as ', $params->realtion_table_name);
        $alias = last($alias);
        $lateralName = $alias.'_lateral';
   

        $lateral = 'lateral jsonb_array_elements('.$temp.') with ordinality as '.$lateralName;
        $params->model->leftJoin(DB::raw($lateral), DB::raw('true'), '=', DB::raw('true'));


        return $params->model->leftJoin($params->realtion_table_name, 
                DB::raw('('.$lateralName.'.value->>0)::bigint'),
                '=',
                $alias.'.'.get_attr_from_cache('columns', 'id', $params->join->join_column_id, 'name'));
    } 
}