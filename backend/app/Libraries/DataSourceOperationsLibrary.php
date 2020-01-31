<?php

namespace App\Libraries;

use App\BaseModel;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DB;

class DataSourceOperationsLibrary 
{
    /****  Main Subscriber Functions  ****/
    
    public function TableEvent($params)
    {
        return $this->{'TableEventFor'.ucfirst($params['type'])}($params);
    }
    
    
    
    /****    Table Events    ****/
    
    private function TableEventForCreate($params)
    {
        $dbTypeName = get_attr_from_cache('data_source_types', 'id', $params['record']->data_source_type_id, 'name');
        return $this->{'UpdateRemoteTablesAndColumnsFor'.ucfirst($dbTypeName)}($params['record']);
    }
    
    private function TableEventForClone($params)
    {
        dd(__FUNCTION__);
    }
    
    private function TableEventForUpdate($params)
    {
        $dbTypeName = get_attr_from_cache('data_source_types', 'id', $params['record']->data_source_type_id, 'name');
        return $this->{'UpdateRemoteTablesAndColumnsFor'.ucfirst($dbTypeName)}($params['record']);
    }
    
    private function TableEventForDelete($params)
    {
        dd(__FUNCTION__);
    }
    
    
    private function TableEventForRestore($params)
    {
        dd(__FUNCTION__);
    }
    
    private function CreatePGDBConnectionForCurrentDataSource($dataSource)
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
    }
    
    private function getTableNamesFromDB($connection)
    {
        return $connection->getDoctrineSchemaManager()->listTableNames();
    }
    
    private function tablesSyncOnDB($dataSource, $tableNames)
    {
        $userId = \Auth::user()->id;
        
        DB::table('data_source_remote_tables')->where('data_source_id', $dataSource->id)->update(
        [
            'state' => FALSE
        ]);
        
        $return = [];        
        foreach($tableNames as $tableName)
        {      
            $control = DB::table('data_source_remote_tables')->where('name_basic', $tableName)->where('data_source_id', $dataSource->id)->get();
            if(count($control) == 0)
                $return[$tableName] = DB::table('data_source_remote_tables')->insertGetId(
                [
                    'data_source_id' => $dataSource->id,
                    'name_basic' => $tableName,
                    'state' => TRUE,
                    'user_id' => $userId,
                    'own_id' => $userId
                ]);
            else if(count($control) != 1) 
                custom_abort('data_source_remote_tables.have.multi.record.for.'.$tableName);
            else
            {
                DB::table('data_source_remote_tables')->where('id', $control[0]->id)->update(
                [
                    'state' => TRUE
                ]);
                $return[$tableName] = $control[0]->id;
            }
        }
        
        return $return;
    }
    
    private function columnsSyncOnDB($connection, $dataSource, $table)
    {
        $userId = \Auth::user()->id;
        
        $sql = 'SELECT column_name as name, data_type as type, udt_name FROM information_schema.columns';
        $sql .= ' WHERE table_schema = \''.env('DB_SCHEMA', 'public').'\' AND ';
        $sql .= ' table_name   = \''.$table[0].'\'';
        
        $columns = $connection->select($sql);
        
        DB::table('data_source_remote_columns')->where('data_source_rmt_table_id', $table[1])->update(
        [
            'state' => FALSE
        ]);
        
        $return = [];        
        foreach($columns as $i => $column)
        {      
            $control = DB::table('data_source_remote_columns')->where('name_basic', $column->name)->where('data_source_rmt_table_id', $table[1])->get();
            if(count($control) == 0)
                $return[$i] = DB::table('data_source_remote_columns')->insertGetId(
                [
                    'data_source_rmt_table_id' => $table[1],
                    'name_basic' => $column->name,
                    'db_type_name' => $column->type,
                    'state' => TRUE,
                    'user_id' => $userId,
                    'own_id' => $userId
                ]);
            else if(count($control) != 1) 
                custom_abort('data_source_remote_columns.have.multi.record.for.'.$column->name);
            else
            {
                DB::table('data_source_remote_columns')->where('id', $control[0]->id)->update(
                [
                    'state' => TRUE
                ]);
                $return[$i] = $control[0]->id;
            }
        }
        
        return $return;
    }
    
    private function UpdateRemoteTablesAndColumnsForPostgresql($dataSource)
    {
        helper('data_entegrator_log', 
        [
            'Update remote tables and columns for pg started', 
            $dataSource
        ]);
        
        $this->CreatePGDBConnectionForCurrentDataSource($dataSource);
        $connection = DB::connection('currentDataSource');  
        
        $tableNames = $this->getTableNamesFromDB($connection);
        $tables = $this->tablesSyncOnDB($dataSource, $tableNames);
        
        $columns = [];
        foreach($tables as $tableName => $tableId)
            $columns[$tableName] = $this->columnsSyncOnDB($connection, $dataSource, [$tableName, $tableId]);
        
        helper('data_entegrator_log', 
        [
            'Update remote tables and columns for pg finished',
            [
                'tables' => $tables, 
                'columns' => $columns
            ]
        ]);
    }
}