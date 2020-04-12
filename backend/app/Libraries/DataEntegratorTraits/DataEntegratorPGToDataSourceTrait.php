<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorPGToDataSourceTrait 
{    
    private function EntegratePostgresqlToDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations)
    {
        $start = 0;
        while(TRUE)
        {
            $records = DB::table($table->name)->limit(100)->offset($start)->get();
            if(count($records) == 0) break;
            $start += 100;
            
            foreach($records as $record)
                $this->EntegratePostgresqlToDataSourceUpdateRecord(
                                                                    $remoteConnection, 
                                                                    $table,
                                                                    $remoteTable, 
                                                                    $columnRelations, 
                                                                    $record);
        }
    }
    
    private function EntegratePostgresqlToDataSourceUpdateRecord($remoteConnection, $table, $remoteTable, $columnRelations, $record)
    {    
        if(strlen($record->remote_record_id) == 0)
        {
            $this->CreateRecordOnPGDataSource($remoteConnection, $remoteTable, $columnRelations, $table, $record);
        }
        else
        {
            $remoteRecord = $this->GetRecordFromPGDataSourceById($remoteConnection, $remoteTable, $record);
            if($remoteRecord == NULL)
                $this->DeleteRecordOnDB($remoteConnection, $remoteTable, $columnRelations, $table, $record);
            else
                $this->UpdateRecordOnPGDataSource($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record);
        }
    }
    
    private function GetRecordFromPGDataSourceById($remoteConnection, $remoteTable, $record)
    {
        $remoteRecord = $remoteConnection->table($remoteTable->name_basic)->where('id', $record->remote_record_id)->get();
        
        if(count($remoteRecord) == 0) 
        {
            helper('data_entegrator_log', ['info', 'There is remote_record_id but there isnt remote record', $remoteTable->name_basic.':'.$record->remote_record_id]);
            return NULL;
        }
        
        return $remoteRecord[0];
    }
}