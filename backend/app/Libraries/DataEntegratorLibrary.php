<?php

namespace App\Libraries;

use App\Libraries\DataEntegratorTraits\DataEntegratorPGTrait;
use Event;
use DB;

class DataEntegratorLibrary
{
    use DataEntegratorPGTrait;
    
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
        $this->ControlRemoteRecordIDColumn($this->tableRelation);
                
        switch ($this->dataSourceType->name) 
        {
            case 'postgresql':
                $this->EntegratePostgresql($this->dataSource, $this->tableRelation, $this->dataEntegratorDirection);
                break;

            default: 
                helper('data_entegrator_log', ['danger', 'Data entegrator invalid datasource type', $this->dataSourceType]);
                dd('invalid.datasourcetype.'.$this->dataSourceType->name);
        }
    }
    
    private function ControlRemoteRecordIDColumn($tableRelation)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        
        $sql = 'SELECT column_name as name, data_type as type, udt_name FROM information_schema.columns';
        $sql .= ' WHERE table_schema = \''.env('DB_SCHEMA', 'public').'\' AND ';
        $sql .= ' table_name   = \''.$table->name.'\'';

        $control = FALSE;
        $columns = \DB::select($sql);
        foreach($columns as $column)
            if($column->name == 'remote_record_id')
                $control = TRUE;
    
        if($control) return;
        
        $this->AddRemoteRecordIDColumn($table);
    }   
    
    private function AddRemoteRecordIDColumn($table)
    {
        DB::statement('ALTER TABLE '.$table->name.' ADD COLUMN remote_record_id integer');
        DB::statement('ALTER TABLE '.$table->name.'_archive ADD COLUMN remote_record_id integer');
        
        $remoteRecordIdColumnId = get_attr_from_cache('columns', 'name', 'remote_record_id', 'id');
        
        $temp = get_model_from_cache('tables', 'name', $table->name);
        $temp->fillVariables();
        $tempColumns = $temp->column_ids;
        array_push($tempColumns, $remoteRecordIdColumnId);
        $temp->column_ids = $tempColumns;
        $temp->save();
    }
    
    private function CreateRecordOnDB($tableName, $data)
    {
        global $pipe;
        $pipe['table'] = $tableName;
        
        $data['column_set_id'] = 0;
        
        $controller = new \App\Http\Controllers\Api\V1\TableController();
        $params = $controller->getValidatedParamsForStore($data, TRUE);
        $record = Event::dispatch('record.store.requested', $params)[1];
        Event::dispatch('record.store.success', [$params, $record]);
        
        $this->UpdateRecordStaticColumns($record, $data);
    }
    
    private function UpdateRecordOnDB($record, $tableName, $data)
    {
        $record = get_model_from_cache($tableName, 'id', $record->id);
        
        global $pipe;
        $pipe['table'] = $tableName;
        
        $data['column_set_id'] = 0;
        
        $controller = new \App\Http\Controllers\Api\V1\TableController();
        $params = $controller->getValidatedParamsForUpdate($data, TRUE);
        
        $orj = $record->toArray();
        $record = Event::dispatch('record.update.requested', [$params, $record])[1];
        Event::dispatch('record.update.success', [$params, $orj, $record]);
        
        $this->UpdateRecordStaticColumns($record, $data);
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
}
