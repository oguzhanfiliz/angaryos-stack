<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;

use App\BaseModel;
use DB;

trait BaseModelGetRelationDataTrait
{
    private function fillRelationData($column)
    {
        $params = helper('get_null_object');
        $params->column = $column;
        $params->record = $this;
        
        ColumnClassificationLibrary::relation(  $this, 
                                                __FUNCTION__, 
                                                $params->column, 
                                                NULL, 
                                                $params);
    }
    
    public function fillRelationDataForDataSource($params)
    {
        $dataSource = $params->relation->getRelationData('data_source_id');
        
        $repository = NULL;
        eval(helper('clear_php_code', $dataSource->php_code));
        dd('repository3');
        
        $params->record->{$params->column->name . '__relation_data'} = $repository->getRecordsBySourceData(json_encode($params->data_array));
        
        /*if(!function_exists('getFromDataSource'.$params->column->name))
        {
            eval(helper('clear_php_code', $dataSource->php_code));
        }
        
        $params->column->db_type_name = $params->column->getRelationData('column_db_type_id')->name;
        $functionName = 'getFromDataSource'.$params->column->name;
        $params->record->{$params->column->name . '__relation_data'} = $functionName(
                                                                                        __FUNCTION__, 
                                                                                        $params->column->db_type_name, 
                                                                                        json_encode($params->data_array));*/
    }
    
    public function fillRelationDataForTableIdAndColumnIds($params)
    {
        $table = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, 'name');
        $source = get_attr_from_cache('columns', 'id', $params->relation->relation_source_column_id, 'name');
        $display = get_attr_from_cache('columns', 'id', $params->relation->relation_display_column_id, 'name');
        
        $sorted = [];
        $temp = new BaseModel($table);
        $temp = $temp->whereIn($source, $params->data_array)->get();
        foreach($temp as $key => $value)
        {
            $temp[$key]->_source_column = $temp[$key]->{$source};
            $temp[$key]->_display_column = $temp[$key]->{$display};
            $temp[$key]->_source_column_name = $source;
            $temp[$key]->_display_column_name = $display;
            
            $key = (int)array_search($value->id, $params->data_array);
            $sorted[$key] = $value;
        }
        
        $temp = [];
        for($i = 0; $i < count($sorted); $i++)
            array_push ($temp, $sorted[$i]);
        
        if($params->column->column_db_type_id == $params->relation->column_db_type_id) 
            $temp = @$temp[0];
        
        $params->record->{$params->column->name . '__relation_data'} = $temp;
    }
    
    public function fillRelationDataForRelationSql($params)
    {
        if($params->data_array == [])
            $temp = [];
        else
        {   
            $params->data_array = '('.implode(',', $params->data_array).')';
            $sql = $params->record->sql_injection_where(
                    $params->relation->relation_sql, 
                    $params->relation->relation_source_column, 
                    'in', 
                    $params->data_array);

            $temp = DB::select($sql);
        }
        
        foreach($temp as $key => $value)
        {
            $temp[$key]->_source_column = $temp[$key]->{$params->relation->relation_source_column};
            $temp[$key]->_display_column = $temp[$key]->{$params->relation->relation_display_column};
            $temp[$key]->_source_column_name = $params->relation->relation_source_column;
            $temp[$key]->_display_column_name = $params->relation->relation_display_column;
        }
        
        if($params->column->column_db_type_id == $params->relation->column_db_type_id) $temp = @$temp[0];
        
        $params->record->{$params->column->name . '__relation_data'} = $temp; 
    }
    
    private function fill_relation_for_with_join_table_ids($params)
    {
        dd("asdasdasd11");
    }
}