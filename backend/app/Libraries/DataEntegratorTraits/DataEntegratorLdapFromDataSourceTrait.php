<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorLdapFromDataSourceTrait 
{    
    private function EntegrateLdapFromDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction)
    {
        $filter='(cn=*)';
        $remoteRecords = $remoteConnection->search($filter, $remoteTable->name_basic); 
        
        $count = count($remoteRecords);
        $step = 0;
        
        $lastId = -1;
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');

        foreach($remoteRecords as $remoteRecord)
        {
            $this->EntegrateLdapFromDataSourceUpdateRecord(
                                                            $remoteConnection, 
                                                            $tableRelation,
                                                            $table,
                                                            $remoteTable, 
                                                            $columnRelations, 
                                                            $remoteRecord,
                                                            $direction);
            
            $this->WriteDataEntegratorLog($tableRelation, 'fromDataSource', $count, ++$step);
                        
            if($lastId < (int)$remoteRecord[$remoteIdColumnName])
                $lastId = (int)$remoteRecord[$remoteIdColumnName];
        }
        
        if($direction == 'twoWay' || $direction == 'fromDataSource')
            $this->UpdateDataEntegratorStates($remoteConnection, $tableRelation, $lastId);                                                                
    }

    private function EntegrateLdapFromDataSourceUpdateRecord($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $remoteRecord, $direction)
    {    
        $remoteRecord = $this->GetRemoteRecordObjectFromLdapRemoteRecord($remoteConnection, $columnRelations, $remoteRecord);
        
        $record = $this->GetRecordFromDBByRemoteRecordId($tableRelation, $table, $remoteRecord);
        if($record === FALSE) return;
      
        if($direction == 'twoWay' || $direction == 'fromDataSource')
            $newRecord = $this->GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord);
        
        if($record == NULL)
        {
            if($this->RemoteRecordIdIsSmallerThanLastId($remoteConnection, $tableRelation, $remoteRecord->id))
            {
                if($this->DeletedRecordIsDisableEntegrate($tableRelation, $remoteRecord->id)) return;

                if($direction == 'twoWay' || $direction == 'toDataSource') 
                    $this->DeleteRecordOnLdapDataSource($remoteConnection, $remoteRecord);
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