<?php

namespace App\Libraries\DataEntegratorTraits;

use App\Libraries\LdapLibrary;

use Storage;
use DB;

trait DataEntegratorLdapTrait 
{    
    use DataEntegratorLdapFromDataSourceTrait;
    use DataEntegratorLdapToDataSourceTrait;
    //use DataEntegratorLdapTwoWayTrait;
    
    private function EntegrateLdap($dataSource, $tableRelation, $direction)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        $remoteTable = get_attr_from_cache('data_source_remote_tables', 'id', $tableRelation->data_source_rmt_table_id, '*');
        
        $columnRelations = [];
        foreach(json_decode($tableRelation->data_source_col_relation_ids) as $columnRelationId)
            array_push($columnRelations, get_attr_from_cache('data_source_col_relations', 'id', $columnRelationId, '*'));
        
        $remoteConnection = $this->CreateLdapDBConnectionByDataSource($dataSource);
        
        $this->EntegrateLdapToDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction->name); 
        $this->EntegrateLdapFromDataSourceUpdateRecords($remoteConnection, $tableRelation, $table,  $remoteTable, $columnRelations, $direction->name);   
    } 
    
    private function CreateLdapDBConnectionByDataSource($dataSource)
    {
        $connection = new LdapLibrary($dataSource->host, $dataSource->user_name, $dataSource->passw, $dataSource->params);
        $connection->dataSourceRecord = $dataSource;

        return $connection; 
    }  
        
    private function CreateRecordOnLdapDataSource($remoteConnection, $tableRelation, $remoteTable, $columnRelations, $table, $record)
    {
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record); 
        
        $remoteConnection->add($newRecord, $remoteTable->name_basic);
        
        $remoteIdColumnName = $this->GetRelatedColumnName($columnRelations, 'id');
        
        $record->remote_record_ids->{$tableRelation->id} = $newRecord[$remoteIdColumnName];
        $record->disable_data_entegrates->{$tableRelation->id} = FALSE;
        
        $data = 
        [
            'remote_record_ids' => json_encode($record->remote_record_ids),
            'disable_data_entegrates' => json_encode($record->disable_data_entegrates)
        ];
        
        copy_record_to_archive($record, $table->name);
        DB::table($table->name)->where('id', $record->id)->update($data);
    }
    
    private function UpdateRecordOnLdapDataSource($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record)
    {
        if($this->CompareUpdatedAtTime($columnRelations, (Object)$remoteRecord, $record))
            return;
        
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record);
        
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');
        $remoteRecord['id'] = $remoteRecord[$remoteIdColumnName];
        $this->SaveOldDataToLocalFromDataSource((Object)$remoteRecord, $newRecord);
        
        $remoteConnection->delete($remoteRecord['dn']);
        $remoteConnection->add($newRecord, $remoteTable->name_basic);
    }

    private function DeleteRecordOnLdapDataSource($remoteConnection, $remoteRecord)
    {
        $this->SaveOldDataToLocalFromDataSource($remoteRecord, 'delete');
        $remoteConnection->delete($remoteRecord->dn);
    }
}