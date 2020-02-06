<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorPGFromDataSourceTrait 
{    
    private function EntegratePostgresqlFromDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations)
    {
        $start = 0;
        while(TRUE)
        {
            $remoteRecords = $remoteConnection->table($remoteTable->name_basic)->limit(100)->offset($start)->get();
            if(count($remoteRecords) == 0) break;
            $start += 100;
            
            foreach($remoteRecords as $remoteRecord)
                $this->EntegratePostgresqlFromDataSourceUpdateRecord(
                                                                    $remoteConnection, 
                                                                    $table,
                                                                    $remoteTable, 
                                                                    $columnRelations, 
                                                                    $remoteRecord);
        }
    }
    
    private function EntegratePostgresqlFromDataSourceUpdateRecord($remoteConnection, $table, $remoteTable, $columnRelations, $remoteRecord)
    {    
        $currentRecords = $this->GetRecordsFromDBByRemoteRecordId($table, $remoteRecord);
        if($currentRecords === FALSE) return;
      
        $count = count($currentRecords);
        if($count == 1)
            if($this->CompareUpdatedAtTime($columnRelations, $currentRecords[0], $remoteRecord))
                return;
        
        $newRecord = $this->GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord);
                
        if($count == 0)
            $this->CreateRecordOnDB($table->name, $newRecord);
        else if($count == 1)
            $this->UpdateRecordOnDB($currentRecords[0], $table->name, $newRecord);
    }
    
    private function GetRecordsFromDBByRemoteRecordId($table, $remoteRecord)
    {
        $currentRecords = DB::table($table->name)->where('remote_record_id', $remoteRecord->id)->get();
        $count = count($currentRecords);
        
        if($count > 1) 
        {
            helper('data_entegrator_log', ['warning', 'Multi record has same remote_record_id', $table->name.':'.$remoteRecord->id]);
            return FALSE;
        }
        
        return $currentRecords;
    }
    
    private function GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord)
    {
        $direction = 'fromDataSource';//using in eval()
        
        $newRecord['remote_record_id'] = $remoteRecord->id;
        foreach($columnRelations as $columnRelation)
        {
            $columnName = get_attr_from_cache('columns', 'id', $columnRelation->column_id, 'name');
            $remoteColumnName = get_attr_from_cache('data_source_remote_columns', 'id', $columnRelation->data_source_remote_column_id, 'name_basic');
            
            try 
            {
                $data = $remoteRecord->{$remoteColumnName};
                
                if(strlen($columnRelation->php_code) > 0)
                    eval(helper('clear_php_code', $columnRelation->php_code)); 
                
                $newRecord[$columnName] = $data;
            } 
            catch (\Error  $ex) 
            {
                throw new \Exception('Error in eval (data_source_col_relations:'.$columnRelation->id.'): '.$ex->getMessage());
            }
            
        }
        
        if(!isset($newRecord['user_id'])) $newRecord['user_id'] = ROBOT_USER_ID;
        if(!isset($newRecord['own_id'])) $newRecord['own_id'] = ROBOT_USER_ID;
        
        return $newRecord;
    }
}