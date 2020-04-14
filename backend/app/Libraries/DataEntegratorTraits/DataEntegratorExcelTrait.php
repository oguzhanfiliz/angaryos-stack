<?php

namespace App\Libraries\DataEntegratorTraits;

use App\Libraries\LdapLibrary;

use Storage;
use DB;

trait DataEntegratorExcelTrait 
{    
    use DataEntegratorExcelFromDataSourceTrait;
    use DataEntegratorExcelToDataSourceTrait;
    
    private function EntegrateExcel($dataSource, $tableRelation, $direction)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        $remoteTable = get_attr_from_cache('data_source_remote_tables', 'id', $tableRelation->data_source_rmt_table_id, '*');
        
        $columnRelations = [];
        foreach(json_decode($tableRelation->data_source_col_relation_ids) as $columnRelationId)
            array_push($columnRelations, get_attr_from_cache('data_source_col_relations', 'id', $columnRelationId, '*'));
        
        $remoteConnection = $this->CreateExcelFakeConnectionByDataSource($dataSource);
        
        $this->EntegrateExcelFromDataSourceUpdateRecords($remoteConnection, $tableRelation, $table,  $remoteTable, $columnRelations, $direction->name);   
        $this->EntegrateExcelToDataSourceUpdateRecords($remoteConnection, $tableRelation, $table,  $remoteTable, $columnRelations, $direction->name);   
    } 
    
    private function CreateExcelFakeConnectionByDataSource($dataSource)
    {
        $connection = helper('get_null_object');
        $connection->dataSourceRecord = $dataSource;

        return $connection; 
    }  
        
    private function GetRemoteRecordObjectFromExcelRemoteRecord($remoteConnection, $columnRelations, $remoteRecord)
    {
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');
        $remoteRecord['id'] = $remoteRecord[$remoteIdColumnName];
        
        $filePath = storage_path('app').'/'.$remoteConnection->dataSourceRecord->params;
        $remoteRecord['updated_at'] = date("Y-m-d H:i:s", filemtime($filePath));
        
        return (Object)$remoteRecord;
    }
}