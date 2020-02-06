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
            $remoteRecord = $this->GetRecordFromDataSourceById($remoteConnection, $remoteTable, $record);
            if($remoteRecord == NULL)
                $this->CreateRecordOnPGDataSource($remoteConnection, $remoteTable, $columnRelations, $table, $record);
            else
                $this->UpdateRecordOnPGDataSource($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record);
        }
    }
    
    private function GetRecordFromDataSourceById($remoteConnection, $remoteTable, $record)
    {
        $remoteRecord = $remoteConnection->table($remoteTable->name_basic)->where('id', $record->remote_record_id)->get();
        
        if(count($remoteRecord) == 0) 
        {
            helper('data_entegrator_log', ['info', 'There is remote_record_id but there isnt remote record', $remoteTable->name_basic.':'.$record->remote_record_id]);
            return NULL;
        }
        
        return $remoteRecord[0];
    }
    
    private function GetNewRecordDataFromCurrentRecord($columnRelations, $record)
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
    }
}