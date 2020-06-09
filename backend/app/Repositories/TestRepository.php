<?php

namespace App\Repositories;

class TestRepository 
{
    public function getDataForSelectElement($record)
    {
        dd('TestRepository:'.__FUNCTION__);
    }
    
    public function getRecordsBySourceData($data)
    {
        $temp = helper('get_null_object');
        $temp->_source_column = '1';
        $temp->_display_column = 'Bir';

        $temp->_source_column_name = '_source_column';
        $temp->_display_column_name = '_display_column';
        
        $temp->tableName = NULL;
        $temp->recordId = NULL;

        return [$temp];
    }
    
    public function getRecordsForListBySourceData($record, $column)
    {
        $guiType = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
        
        if($guiType == 'select')
            return 'Bir';
        else if($guiType == 'multiselect')
            return [['source' => '1', 'display' => 'Bir']];
        else
            dd('unexpected.gui.type.'.$guiType);
    }
    
    public function searchRecords($serach, $page, $limit = REC_COUNT_PER_PAGE)
    {
        return ['records' => ['1' => 'Bir'], 'more' => FALSE];
    }
    
    public function whereRecords($serach)
    {
        return [1];
    }
    
    public function ClearCache($tableName, $record, $type) { }
}
