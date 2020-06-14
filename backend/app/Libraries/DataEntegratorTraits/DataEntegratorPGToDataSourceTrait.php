<?php

namespace App\Libraries\DataEntegratorTraits;

use Storage;
use DB;

trait DataEntegratorPGToDataSourceTrait 
{    
    private function EntegratePostgresqlToDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction)
    {
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
                    $this->EntegratePostgresqlToDataSourceUpdateRecord(
                                                                    $remoteConnection, 
                                                                    $tableRelation,
                                                                    $table,
                                                                    $remoteTable, 
                                                                    $columnRelations, 
                                                                    $record,
                                                                    $direction);
            }
        }
    }
    
    private function EntegratePostgresqlToDataSourceUpdateRecord($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $record, $direction)
    { 
        if(strlen(@$record->remote_record_ids->{$tableRelation->id}) == 0)
        {
            if($direction == 'twoWay' || $direction == 'toDataSource')
                $this->CreateRecordOnPGDataSource($remoteConnection, $tableRelation, $remoteTable, $columnRelations, $table, $record);
        }
        else
        {
            $remoteRecord = $this->GetRecordFromPGDataSourceById($remoteConnection, $tableRelation, $remoteTable, $record);
            if($remoteRecord == NULL)
            {
                if(@$record->disable_data_entegrates->{$tableRelation->id}) return;
                
                $tableId = get_attr_from_cache('data_source_tbl_relations', 'id', $tableRelation->id, 'table_id');
                $tableName = get_attr_from_cache('tables', 'id', $tableId, 'name');

                if($direction == 'twoWay' || $direction == 'fromDataSource')
                    $this->DeleteRecordOnDB($tableName, $record);
            }
            else
            {
                if($direction == 'twoWay' || $direction == 'toDataSource')
                    $this->UpdateRecordOnPGDataSource($remoteConnection, $tableRelation, $remoteTable, $columnRelations, $remoteRecord, $record);
            }   
        }
    }
    
    private function GetRecordFromPGDataSourceById($remoteConnection, $tableRelation, $remoteTable, $record)
    {
        $remoteRecord = $remoteConnection->table($remoteTable->name_basic)
                            ->where('id', $record->remote_record_ids->{$tableRelation->id})
                            ->get();
        
        if(count($remoteRecord) == 0) return NULL;
        
        return $remoteRecord[0];
    }
}