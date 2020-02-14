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
        $temp->_display_column_name = '_display_column_name';

        return [$temp];
    }
    
    public function getRecordsForListBySourceData($data)
    {
        return ['1' => 'Bir'];
    }
    
    public function searchRecords($serach, $page, $limit = REC_COUNT_PER_PAGE)
    {
        return ['records' => ['1' => 'Bir'], 'more' => FALSE];
    }
    
    public function whereRecords($serach)
    {
        dd('TestRepository:'.__FUNCTION__);
    }
    
    public function ClearCache($tableName, $record, $type) { }
}
