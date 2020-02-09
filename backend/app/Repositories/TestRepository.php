<?php

namespace App\Repositories;

class TestRepository 
{
    public function getDataForSelectElement($record)
    {
        dd(__FUNCTION__);
    }
    
    public function getRecordsBySourceData($data)
    {
        dd(__FUNCTION__);
    }
    
    public function getRecordsForListBySourceData($data)
    {
        dd(__FUNCTION__);
    }
    
    public function searchRecords($serach, $page, $limit = REC_COUNT_PER_PAGE)
    {
        dd(__FUNCTION__);
    }
    
    public function whereRecords($serach)
    {
        dd(__FUNCTION__);
    }
    
    public function ClearCache($tableName, $record, $type)
    {
        //dd(__FUNCTION__);
    }
}
