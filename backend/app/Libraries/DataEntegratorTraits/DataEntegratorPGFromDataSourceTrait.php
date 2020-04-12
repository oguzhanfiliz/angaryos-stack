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
            $remoteRecords = $remoteConnection->table($remoteTable->name_basic)->orderBy('id')->limit(100)->offset($start)->get();
            if(count($remoteRecords) == 0) break;
            $start += 100;
            
            foreach($remoteRecords as $remoteRecord)
                $this->EntegratePostgresqlFromDataSourceUpdateRecord(
                                                                    $remoteConnection, 
                                                                    $table,
                                                                    $remoteTable, 
                                                                    $columnRelations, 
                                                                    $remoteRecord);

            $lastId = $remoteRecords[count($remoteRecords) -1]->id;
            $this->UpdateDataEntegratorStates($remoteConnection, $remoteTable, $lastId);
        }
    }
    
    private function EntegratePostgresqlFromDataSourceUpdateRecord($remoteConnection, $table, $remoteTable, $columnRelations, $remoteRecord)
    {    
        $currentRecords = $this->GetRecordsFromDBByRemoteRecordId($table, $remoteRecord);
        if($currentRecords === FALSE) return;
      
        $count = count($currentRecords);
        if($count == 1)
        {
            if($currentRecords[0]->disable_entegrate)
                return;

            if($this->CompareUpdatedAtTime($columnRelations, $currentRecords[0], $remoteRecord))
                return;
        }
        
        $newRecord = $this->GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord);
        
        if($count == 0)
        {
            if($this->RemoteRecordIdIsSmallerThanLastId($remoteConnection, $remoteRecord->id))
            {
                if($this->DeletedRecordIsDisableEntegrate($remoteRecord)) return;

                $this->DeleteRecordOnPGDataSource($remoteConnection, $remoteTable, $remoteRecord);
            }
            else
                $this->CreateRecordOnDB($table->name, $newRecord);
        }
        else if($count == 1)
            $this->UpdateRecordOnDB($currentRecords[0], $table->name, $newRecord);
    }

    private function RemoteRecordIdIsSmallerThanLastId($remoteConnection, $id)
    {
        global $pipe;

        $dataSourceId = $remoteConnection->dataSourceRecord->id;
        $tableRelationId = $pipe['dataEntegratorCurrentTableRelationId'];
        
        $json = DB::table('settings')->where('name', 'DATA_ENTEGRATOR_STATES')->first()->value;
        $states = json_decode($json, TRUE);
        
        if(!isset($states['DataSources'])) return FALSE;
        if(!isset($states['DataSources'][$dataSourceId])) return FALSE;
        if(!isset($states['DataSources'][$dataSourceId][$tableRelationId ])) return FALSE;

        $lastId = $states['DataSources'][$dataSourceId][$tableRelationId]['lastId'];

        return $id <= $lastId;
    }

    private function DeletedRecordIsDisableEntegrate($remoteRecord)
    {
        global $pipe;
        $tableRelationId = $pipe['dataEntegratorCurrentTableRelationId'];
        $tableId = get_attr_from_cache('data_source_tbl_relations', 'id', $tableRelationId, 'table_id');
        $tableName = get_attr_from_cache('tables', 'id', $tableId, 'name');

        $archive = DB::table($tableName.'_archive')
                        ->where('remote_record_id', $remoteRecord->id)
                        ->orderBy('id', 'desc')->first();

        if($archive == NULL)
            throw new \Exception('Not found deleted record archive! tableName: '.$tableName.', remoteRecord: ' . json_encode($remoteRecord));
        
        return $archive->disable_entegrate;
    }
}