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
                $this->clearFilterCache($record);
                break;
            case 'customlayers':
                $this->clearCustomLayerCache($record);
                break;
            
            case 'column_table_relations':
                $this->clearColumnTableRelationCache($record);
                break;

            case 'column_sets':
            case 'column_arrays':
                $this->clearColumnSetOrArrayCache($record);
                break;
            
            case 'data_filter_types':
            case 'missions':
                Cache::forget('allAuths');
                break;
        }
        
        return TRUE;
    }

    private function clearColumnSetOrArrayCache($setOrArray)
    {
        Cache::forget('allAuths');
        
        $table = get_attr_from_cache('tables', 'id', $setOrArray->table_id, '*');
        $this->clearTableCache($table);
    }

    private function clearColumnTableRelationCache($relation)
    {
        $columns = DB::table('columns')->where('column_table_relation_id', $relation->id)->get(); 
        foreach($columns as $column)
            $this->clearColumnCache($column);
    }
    
    private function clearCustomLayerCache($customLayer)
    {
        Cache::forget('allAuths');
        dd('clearCustomLayerCache');
        //$key = 'customLayerSeoName:'.$seoName.'|returnData:table_id';
    }
    
    private function clearFilterCache($filter)
    {
        Cache::forget('allAuths');
        
        $filterTypeName = get_attr_from_cache('data_filter_types', 'id', $filter->data_filter_type_id, 'name');
        if($filterTypeName != 'list') return;
        
        $keys = $this->getCacheKeys();
        foreach($keys as $key)
            if(strstr($key, 'userToken:'))
                dd('clearFilterCache');//userToken:1111111111111111d1.tableName:test.mapFilters
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
        $keys = $this->getCacheKeys();
        foreach($keys as $key)
            if(substr($key, -16, 16) == '|tableGroups')
                Cache::forget($key);
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
        
        $users = \DB::table('users')
                    ->where('auths', '@>', $record->id)
                    ->orWhere('auths', '@>', '"'.$record->id.'"')
                    ->get();
        
        foreach($users as $user)
            $this->clearUserCache($user);
        
        $keys = $this->getCacheKeys();
        foreach($keys as $key)
            if(strstr($key, 'userToken:'))
                dd('clearFilterCache');//userToken:1111111111111111d1.tableName:test.mapFilters
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
        
        $keys = $this->getCacheKeys();
        foreach($keys as $key)
            if(strstr($key, 'userToken:'))
                dd('clearUserCache');//userToken:1111111111111111d1.tableName:test.mapFilters
            
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
    
    private function clearTableStandartCache($tableId, $tableName)
    {
        Cache::forget('tableName:'.$tableName.'|fillableColumns');
        Cache::forget('tableName:'.$tableName.'|castsColumns');
        Cache::forget('tableName:'.$tableName.'|allColumsFromDb');
        Cache::forget('tableName:'.$tableName.'|allColumsFromDbWithTableAliasAndGuiType');
        
        if(strstr($tableName, '_archive')) return;
        
        $key = 'table:'.$tableName.'|type:column_arrays|id:';
        $columnArrays = DB::table('column_arrays')->where('table_id', $tableId)->get();
        foreach($columnArrays as $columnArray) Cache::forget($key.$columnArray->id);
        Cache::forget($key.'0');
    }
    
    private function clearTablesAndColumnCommonCache($table)
    {
        $this->clearTableStandartCache($table->id, $table->name);
        $this->clearTableStandartCache($table->id, $table->name.'_archive');
        
        $this->clearColumnSetsOrArraysCache($table);
        
        $keys = $this->getCacheKeys();
        foreach($keys as $key)
        {
            if(substr($key, -16, 16) == '|filteredColumns')
            {
                $prefix = 'tableName:'.$table->name.'|columnNames:';
                $prefixArchive = 'tableName:'.$table->name.'_archive|columnNames:';

                if(strstr($key, $prefix) || strstr($key, $prefixArchive))
                { 
                    $key = str_replace(explode($prefix, $key)[0], '', $key);
                    Cache::forget($key);
                }
            }
            else if(strstr($key, 'userToken:'))
            {
                //userToken:1111111111111111d1.tableName:test.mapFilters
                dd('clearTablesAndColumnCommonCache');
            }
            else if(substr($key, -16, 16) == '|tableGroups')
            {
                Cache::forget($key);
            }
            else if(strstr($key, 'customLayerSeoName:'))
            {
                Cache::forget($key);
            }
        }
    }
}
