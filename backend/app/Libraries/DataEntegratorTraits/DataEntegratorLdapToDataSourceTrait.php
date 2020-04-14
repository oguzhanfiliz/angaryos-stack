<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorLdapToDataSourceTrait 
{    
    private function EntegrateLdapToDataSourceUpdateRecords($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $direction)
    {
        $start = 0;
        while(TRUE)
        {
            $records = DB::table($table->name)->orderBy('id')->limit(100)->offset($start)->get();
            if(count($records) == 0) break;
            $start += 100;
            
            $records = $this->UpdateDataEntegratorColumnsData($records);

            foreach($records as $record) 
                if(!@$record->disable_data_entegrates->{$tableRelation->id})
                    $this->EntegrateLdapToDataSourceUpdateRecord(
                                                                $remoteConnection, 
                                                                $tableRelation,
                                                                $table,
                                                                $remoteTable, 
                                                                $columnRelations, 
                                                                $record,
                                                                $direction);
        }
    }
    
    private function EntegrateLdapToDataSourceUpdateRecord($remoteConnection, $tableRelation, $table, $remoteTable, $columnRelations, $record, $direction)
    {    
        if(strlen(@$record->remote_record_ids->{$tableRelation->id}) == 0)
        {
            if($direction == 'twoWay' || $direction == 'toDataSource')
                $this->CreateRecordOnLdapDataSource($remoteConnection, $tableRelation, $remoteTable, $columnRelations, $table, $record);
        }
        else
        {
            $remoteRecord = $this->GetRecordFromLdapDataSourceById($remoteConnection, $tableRelation, $columnRelations, $remoteTable, $record);
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
                    $this->UpdateRecordOnLdapDataSource($remoteConnection, $remoteTable, $columnRelations, $remoteRecord, $record);
            }
        }
    }
    
    private function GetRecordFromLdapDataSourceById($remoteConnection, $tableRelation, $columnRelations, $remoteTable, $record)
    {
        $remoteIdColumnName = $this->getRelatedColumnName($columnRelations, 'id');
        if(strlen($remoteIdColumnName) == 0) 
        {
            helper('data_entegrator_log', ['info', 'There is no id column relation', $remoteTable->name_basic.':'.$record->remote_record_ids]);
            return NULL;
        }
        
        $filter = '('.$remoteIdColumnName.'='.$record->remote_record_ids->{$tableRelation->id}.')';
        $remoteRecord = $remoteConnection->search($filter);
		
        if(count($remoteRecord) == 0) return NULL;
        
        $remoteRecord[0]['updated_at'] = $remoteConnection->getModifyTime($remoteRecord[0]['dn']);
        
        return $remoteRecord[0];
    }
}