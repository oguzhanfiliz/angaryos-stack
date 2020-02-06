<?php

namespace App\Libraries\DataEntegratorTraits;

use App\Libraries\LdapLibrary;

use Storage;
use DB;

trait DataEntegratorLdapTrait 
{    
    use DataEntegratorLdapFromDataSourceTrait;
    use DataEntegratorLdapToDataSourceTrait;
    use DataEntegratorLdapTwoWayTrait;
    
    private function EntegrateLdap($dataSource, $tableRelation, $direction)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        $remoteTable = get_attr_from_cache('data_source_remote_tables', 'id', $tableRelation->data_source_rmt_table_id, '*');
        
        $columnRelations = [];
        foreach(json_decode($tableRelation->data_source_col_relation_ids) as $columnRelationId)
            array_push($columnRelations, get_attr_from_cache('data_source_col_relations', 'id', $columnRelationId, '*'));
        
        $remoteConnection = $this->CreateLdapDBConnectionByDataSource($dataSource);
        
        $this->{'EntegrateLdap'.ucfirst($direction->name).'UpdateRecords'}($remoteConnection, $table, $remoteTable, $columnRelations);
    } 
    
    private function CreateLdapDBConnectionByDataSource($dataSource)
    {
        return (new LdapLibrary($dataSource->host, $dataSource->user_name, $dataSource->passw, $dataSource->params)); 
    }  
        
    private function CreateRecordOnLdapDataSource($remoteConnection, $remoteTable, $columnRelations, $table, $record)
    {
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record); 
        
        @$remoteConnection->add($newRecord, $remoteTable->name_basic);
        
        $remoteIdColumnName = $this->GetRelatedColumnName($columnRelations, 'id');
        copy_record_to_archive($record, $table->name);
        DB::table($table->name)->where('id', $record->id)->update(['remote_record_id' => $newRecord[$remoteIdColumnName]]);
    }
    
    private function UpdateRecordOnLdapDataSource($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record)
    {
        if($this->CompareUpdatedAtTime($columnRelations, (Object)$remoteRecord, $record))
            return;
        
        $newRecord = $this->GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record);
        
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');
        $remoteRecord['id'] = $remoteRecord[$remoteIdColumnName];
        $this->SaveOldDataToLocalFromDataSource((Object)$remoteRecord, $newRecord);
        
        @$remoteConnection->delete($remoteRecord['dn']);
        $remoteConnection->add($newRecord, $remoteTable->name_basic);
    }
}