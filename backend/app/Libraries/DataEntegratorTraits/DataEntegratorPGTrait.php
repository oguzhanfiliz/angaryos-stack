<?php

namespace App\Libraries\DataEntegratorTraits;

use Storage;
use DB;

trait DataEntegratorPGTrait 
{    
    use DataEntegratorPGFromDataSourceTrait;
    use DataEntegratorPGToDataSourceTrait;
    
    private function EntegratePostgresql($dataSource, $tableRelation, $direction)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        $remoteTable = get_attr_from_cache('data_source_remote_tables', 'id', $tableRelation->data_source_rmt_table_id, '*');
        
        $columnRelations = [];
        foreach(json_decode($tableRelation->data_source_col_relation_ids) as $columnRelationId)
            array_push($columnRelations, get_attr_from_cache('data_source_col_relations', 'id', $columnRelationId, '*'));
        
        $remoteConnection = $this->CreatePGDBConnectionByDataSource($dataSource);
        
        $this->EntegratePostgresqlToDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction->name); 
        $this->EntegratePostgresqlFromDataSourceUpdateRecords($remoteConnection, $tableRelation, $table,  $remoteTable, $columnRelations, $direction->name);
    
        $this->WriteDataEntegratorLog($tableRelation, 'success');
    } 
    
    private function CreatePGDBConnectionByDataSource($dataSource)
    {
        $params = explode('|', $dataSource->params);
        
        config(['database.connections.currentDataSource' => 
        [
            'driver' => 'pgsql',
            'host' => $dataSource->host,
            'database' => $params[0],
            'username' => $dataSource->user_name,
            'password' => $dataSource->passw,
            'schema' => $params[1]
        ]]);
        
        $connection = DB::connection('currentDataSource');
        $connection->dataSourceRecord = $dataSource;

        return $connection;  
    } 
    
    private function CreateRecordOnPGDataSource($remoteConnection, $tableRelation, $remoteTable, $columnRelations, $table, $record)
    {
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record);
        
        $newRecordId = $remoteConnection->table($remoteTable->name_basic)->insertGetId($newRecord);
        
        $record->remote_record_ids->{$tableRelation->id} = $newRecordId;
        $record->disable_data_entegrates->{$tableRelation->id} = FALSE;
        
        $data = 
        [
            'remote_record_ids' => json_encode($record->remote_record_ids),
            'disable_data_entegrates' => json_encode($record->disable_data_entegrates)
        ];
        
        copy_record_to_archive($record, $table->name);
        DB::table($table->name)->where('id', $record->id)->update($data);
    }
    
    private function UpdateRecordOnPGDataSource($remoteConnection, $tableRelation, $remoteTable, $columnRelations, $remoteRecord, $record)
    {
        if($this->CompareUpdatedAtTime($columnRelations, $remoteRecord, $record))
            return;
        
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record);
        
        $this->SaveOldDataToLocalFromDataSource($remoteRecord, $newRecord);
        
        $remoteConnection->table($remoteTable->name_basic)
                            ->where('id', $record->remote_record_ids->{$tableRelation->id})
                            ->update($newRecord);
    }
    
    private function DeleteRecordOnPGDataSource($remoteConnection, $remoteTable, $remoteRecord)
    {
        $this->SaveOldDataToLocalFromDataSource($remoteRecord, 'delete');
        
        $remoteConnection->table($remoteTable->name_basic)
                            ->where('id', $remoteRecord->id)
                            ->delete();
    }
}