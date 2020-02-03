<?php

namespace App\Libraries;

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
        DB::beginTransaction();
        
        $this->ControlRemoteRecordIDColumn($this->tableRelation);
                
        switch ($this->dataSourceType->name) 
        {
            case 'postgresql':
                $this->EntegratePostgresql($this->dataSource, $this->tableRelation, $this->dataEntegratorDirection);
                break;

            default: dd('no.entegrate.datasourcetype.'.$this->dataSourceType->name);
        }
        
        dd(99);
        
        DB::commit();
    }
    
    private function ControlRemoteRecordIDColumn($tableRelation)
    {
        $table = get_attr_from_cache('tables', 'id', $tableRelation->table_id, '*');
        if(isset($table->remote_record_id)) return;
        
        DB::statement('ALTER TABLE '.$table->name.' ADD COLUMN remote_table_id integer');
        DB::statement('ALTER TABLE '.$table->name.'_archive ADD COLUMN remote_table_id integer');
    }   
}
