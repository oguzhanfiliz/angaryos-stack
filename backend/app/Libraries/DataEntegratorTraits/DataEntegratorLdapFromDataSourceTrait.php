<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorLdapFromDataSourceTrait 
{    
    private function EntegrateLdapFromDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations)
    {
        $filter='(cn=*)';
        $remoteRecords = $remoteConnection->search($filter, $remoteTable->name_basic); 
        
        foreach($remoteRecords as $remoteRecord)
            $this->EntegrateLdapFromDataSourceUpdateRecord(
                                                                    $remoteConnection, 
                                                                    $table,
                                                                    $remoteTable, 
                                                                    $columnRelations, 
                                                                    $remoteRecord);
    }

    private function EntegrateLdapFromDataSourceUpdateRecord($remoteConnection, $table, $remoteTable, $columnRelations, $remoteRecord)
    {    
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');
        $updatedAt = $remoteConnection->getModifyTime($remoteRecord['dn']);
        
        $remoteRecord['id'] = $remoteRecord[$remoteIdColumnName];
        $remoteRecord['updated_at'] = $updatedAt->toDateTimeString();
        $remoteRecord = (Object)$remoteRecord;
        
        
        $currentRecords = $this->GetRecordsFromDBByRemoteRecordId($table, $remoteRecord);
        if($currentRecords === FALSE) return;
        
        $count = count($currentRecords);
        if($count == 1)
            if($this->CompareUpdatedAtTime($columnRelations, $currentRecords[0], $remoteRecord))
                return;
        
        $newRecord = $this->GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord);
        
        if($count == 0)
            $this->CreateRecordOnDB($table->name, $newRecord);
        else if($count == 1)
        {
            $this->UpdateRecordOnDB($currentRecords[0], $table->name, $newRecord);
        }
    }
}