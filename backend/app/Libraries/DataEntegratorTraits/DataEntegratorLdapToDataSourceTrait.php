<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorLdapToDataSourceTrait 
{    
    private function EntegrateLdapToDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations)
    {
        $start = 0;
        while(TRUE)
        {
            $records = DB::table($table->name)->limit(100)->offset($start)->get();
            if(count($records) == 0) break;
            $start += 100;
            
            foreach($records as $record)
                $this->EntegrateLdapToDataSourceUpdateRecord(
                                                                    $remoteConnection, 
                                                                    $table,
                                                                    $remoteTable, 
                                                                    $columnRelations, 
                                                                    $record);
        }
    }
    
    private function EntegrateLdapToDataSourceUpdateRecord($remoteConnection, $table, $remoteTable, $columnRelations, $record)
    {    
        if(strlen($record->remote_record_id) == 0)
            $this->CreateRecordOnLdapDataSource($remoteConnection, $remoteTable, $columnRelations, $table, $record);
        else
        {
            $remoteRecord = $this->GetRecordFromLdapDataSourceById($remoteConnection, $columnRelations, $remoteTable, $record);
            if($remoteRecord == NULL)
                $this->CreateRecordOnLdapDataSource($remoteConnection, $remoteTable, $columnRelations, $table, $record);
            else
                $this->UpdateRecordOnLdapDataSource($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record);
        }
    }
    
    private function GetRecordFromLdapDataSourceById($remoteConnection, $columnRelations, $remoteTable, $record)
    {
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');
        if(strlen($remoteIdColumnName) == 0) 
        {
            helper('data_entegrator_log', ['info', 'There is no id column relation', $remoteTable->name_basic.':'.$record->remote_record_id]);
            return NULL;
        }
        
        $filter = "($remoteIdColumnName=$record->remote_record_id)";
        $remoteRecord = $remoteConnection->search($filter);
        if(count($remoteRecord) == 0) 
        {
            helper('data_entegrator_log', ['info', 'There is remote_record_id but there isnt remote record', $remoteTable->name_basic.':'.$record->remote_record_id]);
            return NULL;
        }
        
        $remoteRecord[0]['updated_at'] = $remoteConnection->getModifyTime($remoteRecord[0]['dn']);
        
        return $remoteRecord[0];
    }
}