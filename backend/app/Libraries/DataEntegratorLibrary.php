<?php

namespace App\Libraries;

use App\Libraries\DataEntegratorTraits\DataEntegratorPGTrait;
use App\Libraries\DataEntegratorTraits\DataEntegratorLdapTrait;
use App\Libraries\DataEntegratorTraits\DataEntegratorExcelTrait;

use App\Listeners\CacheSubscriber;

use App\BaseModel;

use Storage;
use Event;
use DB;

class DataEntegratorLibrary
{
    use DataEntegratorPGTrait;
    use DataEntegratorLdapTrait;
    use DataEntegratorExcelTrait;
    
    public $tableRelation, $dataSource, $dataSourceType, $dataEntegratorDirection;
    
    public function __construct($tableRelationId) 
    {
        $this->tableRelation = get_attr_from_cache('data_source_tbl_relations', 'id', $tableRelationId, '*');
        $this->dataEntegratorDirection = get_attr_from_cache('data_source_directions', 'id', $this->tableRelation->data_source_direction_id, '*');
        $this->dataSource = get_attr_from_cache('data_sources', 'id', $this->tableRelation->data_source_id, '*');
        $this->dataSourceType = get_attr_from_cache('data_source_types', 'id', $this->dataSource->data_source_type_id, '*');
    }
    
    public function Entegrate()
    {
        $this->ControlDataEntegratorColumns($this->tableRelation);
                
        switch ($this->dataSourceType->name) 
        {
            case 'postgresql':
                $this->EntegratePostgresql($this->dataSource, $this->tableRelation, $this->dataEntegratorDirection);
                break;
            case 'ldap':
                $this->EntegrateLdap($this->dataSource, $this->tableRelation, $this->dataEntegratorDirection);
                break;
            case 'excel':
                $this->EntegrateExcel($this->dataSource, $this->tableRelation, $this->dataEntegratorDirection);
                break;

            default: 
                helper('data_entegrator_log', ['danger', 'Data entegrator invalid datasource type', $this->dataSourceType]);
                dd('invalid.datasourcetype.'.$this->dataSourceType->name);
        }
    }
    
    private function UpdateDataEntegratorColumnsData($records)
    {
        foreach($records as $i => $record)
        {
            if(strlen($records[$i]->remote_record_ids) > 0)
                $records[$i]->remote_record_ids = json_decode($records[$i]->remote_record_ids);
            else 
                $records[$i]->remote_record_ids = helper('get_null_object');
            
            if(strlen($records[$i]->disable_data_entegrates) > 0)
                $records[$i]->disable_data_entegrates = json_decode($records[$i]->disable_data_entegrates);
            else 
                $records[$i]->disable_data_entegrates = helper('get_null_object');
        }

        return $records;
    }
    
    private function ReverseUpdateDataEntegratorColumnsData($records)
    {
        foreach($records as $i => $record)
        {
            $records[$i]->remote_record_ids = json_encode($records[$i]->remote_record_ids);
            $records[$i]->disable_data_entegrates = json_encode($records[$i]->disable_data_entegrates);
        }
        
        return $records;
    }
    
    private function ControlDataEntegratorColumn($table, $columns, $columnName)
    {
        $control = FALSE;
        foreach($columns as $column)
            if($column->name == $columnName)
            {
                $control = TRUE;
                break;
            }
    
        if($control) return;
        
        $this->AddDataEntegratorColumn($table, $columnName);
    }  
    
    private function ControlDataEntegratorColumns($tableRelation)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        
        $sql = 'SELECT column_name as name, data_type as type, udt_name FROM information_schema.columns';
        $sql .= ' WHERE table_schema = \''.env('DB_SCHEMA', 'public').'\' AND ';
        $sql .= ' table_name   = \''.$table->name.'\'';

        $columns = \DB::select($sql);
        
        $this->ControlDataEntegratorColumn($table, $columns, 'remote_record_ids');
        $this->ControlDataEntegratorColumn($table, $columns, 'disable_data_entegrates');
        
        //dd('iki kolon da eklendi mi kontrol et!');//cacheden dolayı olabilir bi sefer eklemedi!
    }  
    
    private function AddDataEntegratorColumn($table, $columnName)
    {
        
        DB::statement('ALTER TABLE '.$table->name.' ADD COLUMN '.$columnName.' jsonb');
        DB::statement('ALTER TABLE '.$table->name.'_archive ADD COLUMN '.$columnName.' jsonb');
        
        $columnId = get_attr_from_cache('columns', 'name', $columnName, 'id');
        
        $temp = new BaseModel('tables');
        $temp = $temp->where('name', $table->name)->first();
        $temp->fillVariables();
        
        $tempColumns = $temp->column_ids;
        array_push($tempColumns, $columnId);
        $temp->column_ids = $tempColumns;
        
        $temp->save(); 
        
        $cacheSubscriber = new CacheSubscriber(TRUE);
        $cacheSubscriber->recordChangedSuccess('tables', $temp, 'update');
    }
    
    
    
    
    
    
    private function GetNewRemoteRecordDataFromCurrentRecord($columnRelations, $record)
    {
        $direction = 'toDataSource';//using in eval()
        
        $newRecord = [];
        $newRecord = $this->GetDefaultNewRecordDataFromCurrentRecord($columnRelations, $record, NULL, $direction, $newRecord, 'record', 'remoteColumnName');
            
        return $newRecord;
    }
    
    private function GetDefaultNewRecordDataFromCurrentRecord($columnRelations, $record, $remoteRecord, $direction, $newRecord, $witchRecordName, $witchColumnName)
    {
        foreach($columnRelations as $columnRelation)
        {
            $columnName = get_attr_from_cache('columns', 'id', $columnRelation->column_id, 'name');
            $remoteColumnName = get_attr_from_cache('data_source_remote_columns', 'id', $columnRelation->data_source_remote_column_id, 'name_basic');
            
            try 
            {
                $data = $$witchRecordName->{$witchColumnName == 'columnName' ? $remoteColumnName : $columnName};
                
                if(strlen($columnRelation->php_code) > 0)
                    eval(helper('clear_php_code', $columnRelation->php_code)); 
                
                if($data == '***') continue;
                
                $newRecord[$$witchColumnName] = $data;
            } 
            catch (\Exception  $ex) 
            {
                throw new \Exception('Error in eval (data_source_col_relations:'.$columnRelation->id.'): '.$ex->getMessage());
            }
            catch (\Error  $ex) 
            {
                throw new \Exception('Error in eval (data_source_col_relations:'.$columnRelation->id.'): '.$ex->getMessage());
            }            
        }
        
        return $newRecord;
    }
        
    
    
    
    
    private function GetRelatedColumnName($columnRelations, $columnName)
    {
        $remoteColumnName = '';
        foreach($columnRelations as $columnRelation)
        {
            $tempColumnName = get_attr_from_cache('columns', 'id', $columnRelation->column_id, 'name');
            if($tempColumnName == $columnName)
            {
                $remoteColumnName = get_attr_from_cache('data_source_remote_columns', 'id', $columnRelation->data_source_remote_column_id, 'name_basic');
                break;
            }
        }
        
        if($remoteColumnName == '')
            throw new \Exception('Remote table relation not has '.$columnName.' column relation. (id:'.$this->tableRelation->id.')');
        
        return $remoteColumnName;
    }
    
    private function CompareUpdatedAtTime($columnRelations, $currentRecord, $remoteRecord)
    {
        $remoteUpdatedAtColumnName = $this->getRelatedColumnName($columnRelations, 'updated_at');
        if(strlen($remoteUpdatedAtColumnName) == 0)
            throw new \Exception('Remote table relation not has updated_at column relation. (id:'.$this->tableRelation->id.')');
        
        return $currentRecord->updated_at >= $remoteRecord->{$remoteUpdatedAtColumnName};        
    }
    
   
    
    
    private function GetRecordFromDBByRemoteRecordId($tableRelation, $table, $remoteRecord)
    {
        $tId = $tableRelation->id;

        $where = 'remote_record_ids @> \'{"'.$tId.'": '.$remoteRecord->id.'}\'';
        $where .= ' or remote_record_ids @> \'{"'.$tId.'": "'.$remoteRecord->id.'"}\'';

        $currentRecords = DB::table($table->name)->whereRaw($where)->get();
        if(count($currentRecords) == 0) return NULL;
        
        else if(count($currentRecords) > 1) 
        {
            helper('data_entegrator_log', ['warning', 'Multi record has same remote_record_ids', $table->name.':'.$remoteRecord->id]);
            return FALSE;
        }
        
        $currentRecords = $this->UpdateDataEntegratorColumnsData($currentRecords);        
        return $currentRecords[0];
    }
    
    
    
    
    
    
    
    
    
    
    private function CreateRecordOnDB($tableRelation, $tableName, $data)
    {
        $temp = helper('get_null_object');
        
        $temp->remote_record_ids = helper('get_null_object');
        $temp->disable_data_entegrates = helper('get_null_object');
        
        $temp->remote_record_ids->{$tableRelation->id} = $data['remote_record_ids'];
        $temp->disable_data_entegrates->{$tableRelation->id} = FALSE;
        
        $temp = $this->ReverseUpdateDataEntegratorColumnsData([$temp])[0];
        
        $data['remote_record_ids'] = $temp->remote_record_ids;
        $data['disable_data_entegrates'] = $temp->disable_data_entegrates;
        
        global $pipe;
        $pipe['table'] = $tableName;
        
        $data['column_set_id'] = 0;
        
        \Request::merge($data);
        
        $controller = new \App\Http\Controllers\Api\V1\TableController();
        $params = $controller->getValidatedParamsForStore($data, TRUE);
        $params->request->remote_record_ids = $data['remote_record_ids'];
        $record = Event::dispatch('record.store.requested', $params)[1];
        Event::dispatch('record.store.success', [$params, $record]);
        
        $this->UpdateRecordStaticColumns($record, $data);
    }
    
    private function UpdateRecordOnDB($tableRelation, $record, $tableName, $data)
    {
        $record->remote_record_ids->{$tableRelation->id} = $data['remote_record_ids'];
        $record = $this->ReverseUpdateDataEntegratorColumnsData([$record])[0];
        $data['remote_record_ids'] = $record->remote_record_ids;
        
        
        $record = get_model_from_cache($tableName, 'id', $record->id);
        
        global $pipe;
        $pipe['table'] = $tableName;
        
        $data['column_set_id'] = 0;
        
        \Request::merge($data);
        
        $controller = new \App\Http\Controllers\Api\V1\TableController();
        $params = $controller->getValidatedParamsForUpdate($data, TRUE);
        
        $orj = $record->toArray();
        $record = Event::dispatch('record.update.requested', [$params, $record])[1];
        Event::dispatch('record.update.success', [$params, $orj, $record]);
        
        
        $this->UpdateRecordStaticColumns($record, $data);
        
        $cacheSubscriber = new CacheSubscriber(TRUE);
        $cacheSubscriber->recordChangedSuccess($tableName, $record, 'update');
    }
    
    private function DeleteRecordOnDB($tableName, $record)
    {
        copy_record_to_archive($record, $tableName); 

        DB::table($tableName)->where('id', $record->id)->delete();
        
        $record = $this->ReverseUpdateDataEntegratorColumnsData([$record])[0];
        
        $cacheSubscriber = new CacheSubscriber(TRUE);
        $cacheSubscriber->recordChangedSuccess($tableName, $record, 'delete');
    }
    
    private function SaveOldDataToLocalFromDataSource($remoteRecord, $newRecord)
    {
        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        
        Storage::disk($disk)
            ->put(
                'dataEntegratorDatas/'
                .$this->dataSource->name.'/'
                .$this->tableRelation->id.'/'
                .$remoteRecord->id.' '.date("Y-m-d h:i:s").'.json', 

                json_encode(['old' => $remoteRecord, 'new' => $newRecord]));
    }
    
    
    
    
    
    
    
    
    private function GetNewRecordDataFromRemoteRecord($columnRelations, $remoteRecord)
    {
        $direction = 'fromDataSource';//using in eval()
        
        $newRecord['remote_record_ids'] = $remoteRecord->id;
        $newRecord = $this->GetDefaultNewRecordDataFromCurrentRecord(
                                                                        $columnRelations, 
                                                                        NULL, 
                                                                        $remoteRecord, 
                                                                        $direction, 
                                                                        $newRecord, 
                                                                        'remoteRecord', 
                                                                        'columnName');
        
        if(!isset($newRecord['user_id'])) $newRecord['user_id'] = ROBOT_USER_ID;
        if(!isset($newRecord['own_id'])) $newRecord['own_id'] = ROBOT_USER_ID;
        
        return $newRecord;
    }
    
    private function UpdateRecordStaticColumns($record, $data)
    {
        $update = [];
        if(isset($data['own_id'])) $update['own_id'] = $data['own_id'];
        if(isset($data['user_id'])) $update['user_id'] = $data['user_id'];
        if(isset($data['updated_at'])) $update['updated_at'] = $data['updated_at'];
        if(isset($data['created_at'])) $update['created_at'] = $data['created_at'];
        
        if($update != [])
            DB::table($record->getTable())->where('id', $record->id)->update($update);
    }
    
    private function RemoteRecordIdIsSmallerThanLastId($remoteConnection, $tableRelation, $id)
    {
        $dataSourceId = $remoteConnection->dataSourceRecord->id;
        $tableRelationId = $tableRelation->id;
        
        $json = DB::table('settings')->where('name', 'DATA_ENTEGRATOR_STATES')->first()->value;
        $states = json_decode($json, TRUE);
        
        if(!isset($states['DataSources'])) return FALSE;
        if(!isset($states['DataSources'][$dataSourceId])) return FALSE;
        if(!isset($states['DataSources'][$dataSourceId][$tableRelationId])) return FALSE;

        $lastId = $states['DataSources'][$dataSourceId][$tableRelationId]['lastId'];

        return $id <= $lastId;
    }
    
    private function UpdateDataEntegratorStates($remoteConnection, $tableRelation, $lastId)
    {
        $dataSourceId = $remoteConnection->dataSourceRecord->id;
        $tableRelationId = $tableRelation->id;
        
        $json = DB::table('settings')->where('name', 'DATA_ENTEGRATOR_STATES')->first()->value;
        $states = json_decode($json, TRUE);
        
        if(!isset($states['DataSources'])) $states['DataSources'] = [];

        if(!isset($states['DataSources'][$dataSourceId]))  
            $states['DataSources'][$dataSourceId] = [];

        if(!isset($states['DataSources'][$dataSourceId][$tableRelationId ])) 
            $states['DataSources'][$dataSourceId][$tableRelationId] = [];

        $states['DataSources'][$dataSourceId][$tableRelationId]['lastId'] =  $lastId;

        DB::table('settings')->where('name', 'DATA_ENTEGRATOR_STATES')->update(
        [
            'value' => json_encode($states)
        ]);
    }
    
    private function DeletedRecordIsDisableEntegrate($tableRelation, $remoteRecordId)
    {
        $tableRelationId = $tableRelation->id;
        $tableId = get_attr_from_cache('data_source_tbl_relations', 'id', $tableRelationId, 'table_id');
        $tableName = get_attr_from_cache('tables', 'id', $tableId, 'name');

        $where = 'remote_record_ids @> \'{"'.$tableRelationId.'": '.$remoteRecordId.'}\'';
        $where .= ' or remote_record_ids @> \'{"'.$tableRelationId.'": "'.$remoteRecordId.'"}\'';

        $archive = DB::table($tableName.'_archive')
                        ->whereRaw($where)
                        ->orderBy('id', 'desc')->first();
        if($archive == NULL)
            throw new \Exception('Not found deleted record archive! tableName: '.$tableName.', remoteRecord: ' . json_encode($remoteRecord));
        
        if(strlen($archive->disable_data_entegrates) == 0) return FALSE;

        $archive->disable_data_entegrates = json_decode($archive->disable_data_entegrates);
        return @$archive->disable_data_entegrates->{$tableRelationId};
    }
    
    private static function WriteDataEntegratorLog($relation, $direction, $count = 0, $step = 0)
    {
        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        
        Storage::disk($disk)->put(
                        'dataentegratorstatus/'.$relation->id.'.status', 
                        $direction.'.'.$count.'.'.$step);
    }
}
