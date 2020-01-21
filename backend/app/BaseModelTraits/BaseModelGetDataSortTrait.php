<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;

use DB;

trait BaseModelGetDataSortTrait 
{    
    public function addSorts($model, $columns, $sorts)
    {
        $added = FALSE;
        foreach($sorts as $name => $direction)
        {
            $direction = $direction ? 'asc' : 'desc';
            $added = TRUE;
            $this->addSort($model, $columns->{$name}, $direction);
        }
        
        if(!$added) $model->orderBy($this->getTable().'.id');
    }
    
    private function addSort($model, $column, $direction)
    {
        $params = helper('get_null_object');
        $params->model = $model;
        $params->column = $column;
        $params->direction = $direction;
        
        ColumnClassificationLibrary::relation($this, __FUNCTION__, $column, NULL, $params);
    }
    
    public function addSortForBasicColumn($params)
    {
        $columnName = $params->column->table_alias . '.' . $params->column->name;        
        $params->model->orderBy($columnName, $params->direction);
    }
    
    public function addSortForRelationSql($params)
    {
        $columnName = $params->column->name.'___sql_relation'.$params->relation->id.'.'.$params->relation->relation_display_column;
        $params->model->orderBy(DB::raw("string_agg($columnName, ' ')"), $params->direction);
    }
    
    public function addSortForJoinedColumn($params)
    {
        $temp = helper('get_column_data_for_joined_column', $params->column->select_raw);
        $params->model->orderBy(DB::raw($temp[0]), $params->direction);
    }
    
    public function addSortForDataSource($params)
    {
        $this->addSortForBasicColumn($params);
    }
    
    public function addSortForTableIdAndColumnIds($params)
    {
        dd('addSortForTableIdAndColumnIds');
        dd($params);
    }
    
    
    
    public function add_sort_for_table_id_and_column_ids($params)
    {
        dd('sort for add_sort_for_table_id_and_column_ids relation');
    }
    
    
    public function add_sort_for_join_table_ids($params)
    {
        dd('sort for add_sort_with_join_table_ids relation');
        //joined column eklenmesi ile ilgili bir fonksiyon var add_sorts_for_joined_column
    }
    
    
}