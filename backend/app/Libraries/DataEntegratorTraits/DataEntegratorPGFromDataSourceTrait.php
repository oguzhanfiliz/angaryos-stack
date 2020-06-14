<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorPGFromDataSourceTrait 
{    
    private function EntegratePostgresqlFromDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction)
    {
        $count = $remoteConnection->table($table->name)->count('id');
        $step = 0;
        
        $start = 0;
        while(TRUE)
        {
            $remoteRecords = $remoteConnection->table($remoteTable->name_basic)->orderBy('id')->limit(100)->offset($start)->get();
            if(count($remoteRecords) == 0) break;
            $start += 100;
            
            foreach($remoteRecords as $remoteRecord)
            {
                $this->EntegratePostgresqlFromDataSourceUpdateRecord(
                                                                    $remoteConnection, 
                                                                    $tableRelation,
                                                                    $table,
                                                                    $remoteTable, 
                                                                    $columnRelations, 
                                                                    $remoteRecord,
                                                                    $direction);
                
                $this->WriteDataEntegratorLog($tableRelation, 'fromDataSource', $count, ++$step);
            }

            $lastId = $remoteRecords[count($remoteRecords) -1]->id;
            
            if($direction == 'twoWay' || $direction == 'fromDataSource')
                $this->UpdateDataEntegratorStates($remoteConnection, $tableRelation, $lastId);
        }
    }
    
    private function EntegratePostgresqlFromDataSourceUpdateRecord($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $remoteRecord, $direction)
    {
        $record = $this->GetRecordFromDBByRemoteRecordId($tableRelation, $table, $remoteRecord);
        if($record === FALSE) return;
      
        $newRecord = $this->GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord);
        
        if($record == NULL)
        {
            if($this->RemoteRecordIdIsSmallerThanLastId($remoteConnection, $tableRelation, $remoteRecord->id))
            {
                if($this->DeletedRecordIsDisableEntegrate($tableRelation, $remoteRecord->id)) return;

                if($direction == 'twoWay' || $direction == 'toDataSource') 
                    $this->DeleteRecordOnPGDataSource($remoteConnection, $remoteTable, $remoteRecord);
            }
            else
            {
                if($direction == 'twoWay' || $direction == 'fromDataSource')
                    $this->CreateRecordOnDB($tableRelation, $table->name, $newRecord);
            }
        }
        else 
        {
            if(@$record->disable_data_entegrates->{$tableRelation->id}) return;
            if($this->CompareUpdatedAtTime($columnRelations, $record, $remoteRecord)) return;
        
            if($direction == 'twoWay' || $direction == 'fromDataSource')
                $this->UpdateRecordOnDB($tableRelation, $record, $table->name, $newRecord);
        }
    }
}