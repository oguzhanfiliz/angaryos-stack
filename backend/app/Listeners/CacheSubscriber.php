<?php

namespace App\Listeners;

use App\BaseModel;
use Cache;
use DB;

class CacheSubscriber 
{
    private $cacheKeys = NULL;
    
    public function __construct() { }
    
    public function recordChangedSuccess($tableName, $record, $type)
    {
        $this->clearRecordCache($record);
        $this->clearRelationDataCache($tableName, $record, $type);
        
        switch($tableName)
        {
            case 'columns':
                $this->clearColumnCache($record);
                break;
            case 'tables':
                $this->clearTableCache($record);
                break;
            case 'table_groups':
                $this->clearTableGroupCache($record);
                break;
            case 'settings':
                $this->clearSettingCache($record);
                break;
            case 'users':
                $this->clearUserCache($record);
                break;
            case 'auth_groups':
                $this->clearAuthGroupsCache($record);
                break;
            
            case 'data_filters':
            case 'data_filter_types':
            case 'column_sets':
            case 'column_arrays':
            case 'missions':
                Cache::forget('allAuths');
                break;
        }
        
        return TRUE;
    }
    
    private function getCacheKeys()
    {
        if($this->cacheKeys == NULL)
            $this->cacheKeys = getMemcachedKeys();
        
        return $this->cacheKeys;
    }
    
    public function storeSuccess($params, $record)
    {
        return $this->recordChangedSuccess($params->table->name, $record, 'create');
    }
    
    public function updateSuccess($params, $orj, $record)
    {
        return $this->recordChangedSuccess($params->table->name, $record, 'update');
    }
    
    public function deleteSuccess($record)
    {
        return $this->recordChangedSuccess($record->getTable(), $record, 'delete');
    }
    
    public function cloneSuccess($cloneRecord)
    {
        return $this->recordChangedSuccess($cloneRecord->getTable(), $cloneRecord, 'clone');
    }
    
    public function restoreSuccess($archiveRecord, $record)
    {
        return $this->recordChangedSuccess($record->getTable(), $record, 'restore');
    }
    
    
    
    /****     Common Functions    ****/
    
    private function clearTableGroupCache($record)
    {
        Cache::forget('tableGroups');
    }
    
    private function clearAuthGroupsCache($record)
    {
        Cache::forget('allAuths');
        
        $users = \DB::table('users')->where('auths', '@>', $record->id)->get();
        foreach($users as $user)
            $this->clearUserCache($user);
        
        $groups = \DB::table('auth_groups')->where('auths', '@>', $record->id)->get();
        foreach($groups as $group)
            $this->clearAuthGroupsCache($group);
    }
    
    private function clearRelationDataCache($tableName, $record, $type)
    {
        $tableId = get_attr_from_cache('tables', 'name', $tableName, 'id');
        $relations = DB::table('column_table_relations')->where('relation_table_id', $tableId)->get();        
        foreach($relations as $relation) $this->clearRelationCache($relation, $record);
        
        $relations = DB::table('column_table_relations')
                        ->whereRaw('relation_sql ilike \'% from '.$tableName.'%\'  or relation_sql ilike \'% join '.$tableName.' %\' ')
                        ->get();        
        foreach($relations as $relation) $this->clearRelationCache($relation, $record);
        
        $joins = DB::table('join_tables')->where('join_table_id', $tableId)->get(); 
        foreach($joins as $join)
        {
            $relations = DB::table('column_table_relations')->whereRaw('join_table_ids @> \''.$join->id.'\'::jsonb')->get();    
            foreach($relations as $relation) $this->clearRelationCache($relation, $record);
        }
        
        $dataSourceColumns = DB::table('column_table_relations')->whereRaw('(column_data_source_id::text = \'\') IS FALSE')->get();
        foreach($dataSourceColumns as $dataSourceColumn)
        {
            $dataSourceCode = get_attr_from_cache('column_data_sources', 'id', $dataSourceColumn->column_data_source_id, 'php_code');
            $repository = NULL;
            eval(helper('clear_php_code', $dataSourceCode)); 
            $repository->ClearCache($tableName, $record, $type);
        }
    }
    
    private function clearRelationCache($relation, $record)
    {
        $keys = $this->getCacheKeys();
        
        $columns = DB::table('columns')->where('column_table_relation_id', $relation->id)->get();
        foreach($columns as $column)
        {
            $tables = DB::table('tables')->whereRaw('column_ids @> \''.$column->id.'\'::jsonb')->get();
            foreach($tables as $table)
            {                
                foreach($keys as $key)
                {
                    if(substr($key, -13, 13) != '|relationData') continue;
                    
                    $prefix = 'tableName:'.$table->name.'|columnName:'.$column->name.'|columnData:';
                    
                    if(strstr($key, $prefix))
                    { 
                        $key = str_replace(explode($prefix, $key)[0], '', $key);
                        Cache::forget($key);
                    }
                }
            }
        }
    }
    
    private function clearUserCache($record)
    {
        Cache::forget('tableName:users|id:'.$record->id.'|authTree');
        
        if($record->id == PUBLIC_USER_ID)
            Cache::forget('publicUser');
    }
    
    private function clearSettingCache($record)
    {
        Cache::forget('settings');
    }
    
    private function clearColumnSetsOrArraysCache($table)
    {
        Cache::forget('allAuths');
        
        $ts = ['column_sets', 'column_arrays'];        
        foreach($ts as $t)
        {
            $temp = DB::table($t)->where('table_id', $table->id)->get();
            foreach($temp as $rec)
            {
                $key = 'table:'.$table->name.'|type:'.$t.'|id:'.$rec->id; 
                Cache::forget($key);
            }
        }
    }
    
    private function clearRecordCache($record)
    {
        $tableName = $record->getTable();
        $data = $record->toArray();
        
        foreach($data as $columnName => $value)
        {
            if(is_array($value)) $value = json_encode($value);
            
            $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:BaseModel';
            Cache::forget($cacheKey);
            
            $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:*';
            Cache::forget($cacheKey);
            
            $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|relationData';
            Cache::forget($cacheKey);
            
            foreach($data as $returnColumnName => $v)
            {
                $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:'.$returnColumnName;
                Cache::forget($cacheKey);
            }
        }
    }
    
    private function clearColumnCache($record)
    {
        $tableModel = new BaseModel('tables');
        $tables = $tableModel->whereRaw('column_ids @> \''.$record->id.'\'::jsonb')->get();
        foreach($tables as $table)
            $this->clearTablesAndColumnCommonCache($table);
    }
    
    public function clearTableCache($table)
    {
        Cache::forget('allAuths');
        Cache::forget('tableName:'.$table->name.'|tableInfo');
        
        $this->clearTablesAndColumnCommonCache($table);
    }
    
    private function clearTablesAndColumnCommonCache($table)
    {
        Cache::forget('tableName:'.$table->name.'|fillableColumns');
        Cache::forget('tableName:'.$table->name.'|castsColumns');
        Cache::forget('tableName:'.$table->name.'|allColumsFromDb');
        Cache::forget('tableName:'.$table->name.'|allColumsFromDbWithTableAliasAndGuiType');
        
        Cache::forget('tableName:'.$table->name.'_archive|fillableColumns');
        Cache::forget('tableName:'.$table->name.'_archive|castsColumns');
        Cache::forget('tableName:'.$table->name.'_archive|allColumsFromDb');
        Cache::forget('tableName:'.$table->name.'_archive|allColumsFromDbWithTableAliasAndGuiType');
        
        $keys = $this->getCacheKeys();
        foreach($keys as $key)
        {
            if(substr($key, -16, 16) != '|filteredColumns') continue;
                    
            $prefix = 'tableName:'.$table->name.'|columnName:';
            $prefixArchive = 'tableName:'.$table->name.'_archive|columnName:';
            
            if(strstr($key, $prefix) || strstr($key, $prefixArchive))
            { 
                dd('clearTablesAndColumnCommonCache');
                $key = str_replace(explode($prefix, $key)[0], '', $key);
                dd($key);
                Cache::forget($key);
            }
        }
        
        $this->clearColumnSetsOrArraysCache($table);
    }
}
