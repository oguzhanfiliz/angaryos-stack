<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorExcelFromDataSourceTrait 
{    
    private function EntegrateExcelFromDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction)
    {
        $filePath = storage_path('app').'/'.$remoteConnection->dataSourceRecord->params;
        if(!file_exists($filePath))
            throw new \Exception('File not found:'.$filePath);
        
        $data = helper('get_data_from_excel_file', $filePath);
        $remoteRecords = $data[$remoteTable->name_basic]['data'];
        
        $count = count($remoteRecords);
        $step = 0;
        
        $lastId = -1;
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');

        foreach($remoteRecords as $remoteRecord)
        { 
            $this->EntegrateExcelFromDataSourceUpdateRecord(
                                                            $remoteConnection, 
                                                            $tableRelation,
                                                            $table,
                                                            $remoteTable, 
                                                            $columnRelations, 
                                                            $remoteRecord);
            
            $this->WriteDataEntegratorLog($tableRelation, 'fromDataSource', $count, ++$step);
                        
            if($lastId < (int)$remoteRecord[$remoteIdColumnName])
                $lastId = (int)$remoteRecord[$remoteIdColumnName];
        }
        
        $this->UpdateDataEntegratorStates($remoteConnection, $tableRelation, $lastId);                                                                
    }

    private function EntegrateExcelFromDataSourceUpdateRecord($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $remoteRecord)
    {    
        $remoteRecord = $this->GetRemoteRecordObjectFromExcelRemoteRecord($remoteConnection, $columnRelations, $remoteRecord);
        
        $record = $this->GetRecordFromDBByRemoteRecordId($tableRelation, $table, $remoteRecord);
        if($record === FALSE) return;
    
        $newRecord = $this->GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord);
        
        if($record == NULL)
        {
            if(!$this->RemoteRecordIdIsSmallerThanLastId($remoteConnection, $tableRelation, $remoteRecord->id))
            {
                $this->CreateRecordOnDB($tableRelation, $table->name, $newRecord);
            }
        }   
        else
        {
            if(@$record->disable_data_entegrates->{$tableRelation->id}) return;
            if($this->CompareUpdatedAtTime($columnRelations, $record, $remoteRecord)) return;
        
            $this->UpdateRecordOnDB($tableRelation, $record, $table->name, $newRecord);
        }
    }
}