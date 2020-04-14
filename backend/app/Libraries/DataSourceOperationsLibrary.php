<?php

namespace App\Libraries;

use App\BaseModel;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Libraries\LdapLibrary;

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
        $dbTypeName = get_attr_from_cache('data_source_types', 'id', $params['record']->data_source_type_id, 'name');
        return $this->{'UpdateRemoteTablesAndColumnsFor'.ucfirst($dbTypeName)}($params['record']);
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
    
    
    
    /****    Common Functions    ****/
    
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
    
    private function columnsSyncOnDB($dataSource, $table, $columns)
    {
        $userId = \Auth::user()->id;
        
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
    
    
    
    /****    Postgrtesql    ****/
    
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
    
    private function GetTableNamesFromPGDB($connection)
    {
        $return = $connection->getDoctrineSchemaManager()->listTableNames();
        
        $temp = $connection->getDoctrineSchemaManager()->listViews();
        $temp = array_keys($temp);
        foreach($temp as $viewName)
        {
            $viewName = explode('.', $viewName);
            if($viewName[0] != DB_SCHEMA) continue;
            
            array_push($return, $viewName[1]);
        }
        
        return $return;
    }
    
    private function getColumnsFromPGDB($connection, $dataSource, $table)
    {
        $sql = 'SELECT column_name as name, data_type as type, udt_name FROM information_schema.columns';
        $sql .= ' WHERE table_schema = \''.env('DB_SCHEMA', 'public').'\' AND ';
        $sql .= ' table_name   = \''.$table[0].'\'';
        
        $columns = $connection->select($sql);
        return $columns; 
    }
    
    private function UpdateRemoteTablesAndColumnsForPostgresql($dataSource)
    {
        helper('data_entegrator_log', 
        [
            'info',
            'Update remote tables and columns for pg started', 
            $dataSource
        ]);
        
        $this->CreatePGDBConnectionForCurrentDataSource($dataSource);
        $connection = DB::connection('currentDataSource');  
        
        $tableNames = $this->GetTableNamesFromPGDB($connection);
        $tables = $this->tablesSyncOnDB($dataSource, $tableNames);
        
        $columns = [];
        foreach($tables as $tableName => $tableId)
        {
            $temp = $this->getColumnsFromPGDB($connection, $dataSource, [$tableName, $tableId]);
            $columns[$tableName] = $this->columnsSyncOnDB($dataSource, [$tableName, $tableId], $temp);
        }
        
        helper('data_entegrator_log', 
        [
            'info',
            'Update remote tables and columns for pg finished',
            [
                'tables' => $tables, 
                'columns' => $columns
            ]
        ]);
    }
    
    
    
    /****    Excel    ****/
    private function UpdateRemoteTablesAndColumnsForExcel($dataSource)
    {
        helper('data_entegrator_log', 
        [
            'info',
            'Update remote tables and columns for excel started', 
            $dataSource
        ]);
        
        $filePath = storage_path('app').'/'.$dataSource->params;
        if(!file_exists($filePath))
            custom_abort('file.not.found: '.$filePath);
        
        $data = helper('get_data_from_excel_file', $filePath);
        
        $tableNames = array_keys($data);
        $tables = $this->tablesSyncOnDB($dataSource, $tableNames);
        
        $columns = [];
        foreach($tables as $tableName => $tableId)
        {
            $temp = $this->getColumnsFromExcel($data[$tableName]['columns']);
            $columns[$tableName] = $this->columnsSyncOnDB($dataSource, [$tableName, $tableId], $temp);
        }
        
        helper('data_entegrator_log', 
        [
            'info',
            'Update remote tables and columns for pg finished',
            [
                'tables' => $tables, 
                'columns' => $columns
            ]
        ]);
    }
    
    private function getColumnsFromExcel($columns)
    {
        $rt = [];
        foreach ($columns as $column) 
        {
            $temp = helper('get_null_object');
            $temp->name = $column;
            $temp->type = 'mixed';
            
            array_push($rt, $temp);
        }
        
        return $rt; 
    }
    
    
    
    /****    Ldap    ****/
    
    private function UpdateRemoteTablesAndColumnsForLdap($dataSource)
    {
        helper('data_entegrator_log', 
        [
            'info',
            'Update remote tables and columns for ldap started', 
            $dataSource
        ]);
        
        $connection = $this->GetLdapDBConnectionForCurrentDataSource($dataSource);
        
        $tableNames = $this->GetTableNamesFromLdapDB($connection);
        $tables = $this->tablesSyncOnDB($dataSource, $tableNames);
        
        $columns = [];
        foreach($tables as $tableName => $tableId)
        {
            $temp = $this->getColumnsFromLdapDB($connection, $dataSource, [$tableName, $tableId]);
            $columns[$tableName] = $this->columnsSyncOnDB($dataSource, [$tableName, $tableId], $temp);
        }
        
        helper('data_entegrator_log', 
        [
            'info',
            'Update remote tables and columns for pg finished',
            [
                'tables' => $tables, 
                'columns' => $columns
            ]
        ]);
    }
    
    private function GetLdapDBConnectionForCurrentDataSource($dataSource)
    {
        return (new LdapLibrary($dataSource->host, $dataSource->user_name, $dataSource->passw, $dataSource->params));
    }
    
    private function GetTableNamesFromLdapDB($connection)
    {
        $filter='(ou=*)';
        
        $entries = $connection->search($filter); 
        
        $tableNames = [];
        foreach($entries as $entry)
                array_push($tableNames, $entry['dn']);
        
        return $tableNames;
    }
    
    private function getColumnsFromLdapDB($connection, $dataSource, $table)
    {
        $filter='(cn=*)';
        $entries = $connection->search($filter, $table[0]); 
        if(count($entries) == 0) return [];
        
        $columns = [];
        foreach(array_keys($entries[0]) as $key)
        {
            $temp = helper('get_null_object');
            $temp->type = 'string';
            $temp->name = $key;
            
            array_push ($columns, $temp);
        }
        
        $temp = helper('get_null_object');
        $temp->type = 'datetime';
        $temp->name = 'updated_at';

        array_push ($columns, $temp);
            
        return $columns;
    }
}