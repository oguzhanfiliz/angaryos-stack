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
    
    /*private function GetNewRecordDataFromCurrentRecordForPG($columnRelations, $record)
    {
        $direction = 'toDataSource';//using in eval()
        
        $newRecord = [];
        foreach($columnRelations as $columnRelation)
        {
            $columnName = get_attr_from_cache('columns', 'id', $columnRelation->column_id, 'name');
            $remoteColumnName = get_attr_from_cache('data_source_remote_columns', 'id', $columnRelation->data_source_remote_column_id, 'name_basic');
            
            try 
            {
                $data = $record->{$columnName};
                
                if(strlen($columnRelation->php_code) > 0)
                    eval(helper('clear_php_code', $columnRelation->php_code)); 
                
                $newRecord[$remoteColumnName] = $data;
            } 
            catch (\Error  $ex) 
            {
                throw new \Exception('Error in eval (data_source_col_relations:'.$columnRelation->id.'): '.$ex->getMessage());
            }
            
        }
        
        return $newRecord;
    }*/
    
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
}