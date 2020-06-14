<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorExcelToDataSourceTrait 
{    
    private function EntegrateExcelToDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction)
    {
        $filePath = storage_path('app').'/'.$remoteConnection->dataSourceRecord->params;
        if(!file_exists($filePath))
            throw new \Exception('File not found:'.$filePath);
        
        $data = helper('get_data_from_excel_file', $filePath);
        $remoteRecords = $data[$remoteTable->name_basic]['data'];
        
        $count = DB::table($table->name)->count('id');
        $step = 0;
        
        $start = 0;
        while(TRUE)
        {
            $records = DB::table($table->name)->orderBy('id')->limit(100)->offset($start)->get();
            if(count($records) == 0) break;
            $start += 100;
            
            $records = $this->UpdateDataEntegratorColumnsData($records);

            foreach($records as $record) 
            {
                $this->WriteDataEntegratorLog($tableRelation, 'toDataSource', $count, ++$step);
                
                if(!@$record->disable_data_entegrates->{$tableRelation->id})
                    $this->EntegrateExcelToDataSourceUpdateRecord(
                                                                $remoteConnection, 
                                                                $tableRelation,
                                                                $remoteRecords,
                                                                $table,
                                                                $remoteTable, 
                                                                $columnRelations, 
                                                                $record);
            }
        }
    }
    
    private function EntegrateExcelToDataSourceUpdateRecord($remoteConnection, $tableRelation, $remoteRecords, $table, $remoteTable, $columnRelations, $record)
    {    
        if(strlen(@$record->remote_record_ids->{$tableRelation->id}) > 0)
        {
            $remoteRecord = $this->GetRecordFromExcelDataSourceById($remoteConnection, $tableRelation, $remoteRecords, $columnRelations, $remoteTable, $record);
            if($remoteRecord == NULL)
            {
                if(@$record->disable_data_entegrates->{$tableRelation->id}) return;
                
                $tableId = get_attr_from_cache('data_source_tbl_relations', 'id', $tableRelation->id, 'table_id');
                $tableName = get_attr_from_cache('tables', 'id', $tableId, 'name');

                $this->DeleteRecordOnDB($tableName, $record);
            }
        }
    }
    
    private function GetRecordFromExcelDataSourceById($remoteConnection, $tableRelation, $remoteRecords, $columnRelations, $remoteTable, $record)
    {
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');
        if(strlen($remoteIdColumnName) == 0) 
        {
            helper('data_entegrator_log', ['info', 'There is no id column relation', $remoteTable->name_basic.':'.json_encode($record->remote_record_ids)]);
            return NULL;
        }
        
        $remoteRecord = NULL;
        foreach ($remoteRecords as $temp) 
        {
            $local = $record->remote_record_ids->{$tableRelation->id};
            $remote = $temp[$remoteIdColumnName];
            
            if($local == $remote)
            {
                $remoteRecord = $temp;
                break;
            }
        }
        
        if($remoteRecord == NULL) return NULL;
        
        $filePath = storage_path('app').'/'.$remoteConnection->dataSourceRecord->params;
        $remoteRecord['updated_at'] = date("Y-m-d H:i:s", filemtime($filePath));
        
        return $remoteRecord;
    }
}