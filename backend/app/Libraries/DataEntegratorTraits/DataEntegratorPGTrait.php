<?php

namespace App\Libraries\DataEntegratorTraits;

use Storage;
use DB;

trait DataEntegratorPGTrait 
{    
    use DataEntegratorPGFromDataSourceTrait;
    use DataEntegratorPGToDataSourceTrait;
    use DataEntegratorPGTwoWayTrait;
    
    private function EntegratePostgresql($dataSource, $tableRelation, $direction)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        $remoteTable = get_attr_from_cache('data_source_remote_tables', 'id', $tableRelation->data_source_rmt_table_id, '*');
        
        $columnRelations = [];
        foreach(json_decode($tableRelation->data_source_col_relation_ids) as $columnRelationId)
            array_push($columnRelations, get_attr_from_cache('data_source_col_relations', 'id', $columnRelationId, '*'));
        
        $remoteConnection = $this->CreatePGDBConnectionByDataSource($dataSource);
        
        $this->{'EntegratePostgresql'.ucfirst($direction->name).'UpdateRecords'}($remoteConnection, $table, $remoteTable, $columnRelations);
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
        
        return DB::connection('currentDataSource');  
    } 
    
    private function CreateRecordOnPGDataSource($remoteConnection, $remoteTable, $columnRelations, $table, $record)
    {
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record);
        
        $newRecordId = $remoteConnection->table($remoteTable->name_basic)->insertGetId($newRecord);

        copy_record_to_archive($record, $table->name);        
        DB::table($table->name)->where('id', $record->id)->update(['remote_record_id' => $newRecordId]);
    }
    
    private function UpdateRecordOnPGDataSource($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record)
    {
        if($this->CompareUpdatedAtTime($columnRelations, $remoteRecord, $record))
            return;
        
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record);
        
        $this->SaveOldDataToLocalFromDataSource($remoteRecord, $newRecord);
        
        $newRecordId = $remoteConnection->table($remoteTable->name_basic)->where('id', $record->remote_record_id)->update($newRecord);
    }
    
    private function DeleteRecordOnDB($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record)
    {
        dd('DeleteRecordOnDB');//önce arşive al sonra sil çünkü diğer taraftan silinmiş
        if($this->CompareUpdatedAtTime($columnRelations, $remoteRecord, $record))
            return;
        
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record);
        
        $this->SaveOldDataToLocalFromDataSource($remoteRecord, $newRecord);
        
        $newRecordId = $remoteConnection->table($remoteTable->name_basic)->where('id', $record->remote_record_id)->update($newRecord);
    }
}