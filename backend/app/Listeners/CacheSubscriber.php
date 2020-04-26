<?php

namespace App\Listeners;

use App\Jobs\ClearCache;

use App\BaseModel;
use Cache;
use DB;

class CacheSubscriber 
{
    private $cacheKeys = NULL;
    
    public function __construct() { }
    
    public function recordChangedSuccess($tableName, $record, $type)
    {
        if(strstr(get_class($record), 'BaseModel')) $data = $record->toArray();
        else $data = (array)$record;
        $this->clearRecordCache($tableName, $data);
        
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
                ClearCache::dispatch('allAuths');
                break;
        }
        
        return TRUE;
    }

    private function clearColumnSetOrArrayCache($setOrArray)
    {
        ClearCache::dispatch('allAuths');
        
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
        ClearCache::dispatch('allAuths');
        dd('clearCustomLayerCache');
        //$key = 'customLayerSeoName:'.$seoName.'|returnData:table_id';
    }
    
    private function clearFilterCache($filter)
    {
        ClearCache::dispatch('allAuths');
        
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
                ClearCache::dispatch($key);
    }
    
    private function clearAuthGroupsCache($record)
    {
        ClearCache::dispatch('allAuths');
        
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
        $tables = [];
        foreach($record->auths as $auth)
            if(!is_int($auth))
            {
                $temp = explode(':', $auth);
                if($temp[0] != 'tables') continue;
                if(in_array($temp[1], $tables)) continue;
                
                array_push($tables, $temp[1]);
            }
        
        if(count($tables) == 0) return;
        
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
                        ClearCache::dispatch($key);
                    }
                }
            }
        }
    }
    
    private function clearUserCache($record)
    {
        ClearCache::dispatch('tableName:users|id:'.$record->id.'|authTree');
        
        $keys = $this->getCacheKeys();
        foreach($keys as $key)
            if(strstr($key, 'userToken:'))
            {
                //userToken:1111111111111111d1.tableName:test.mapFilters

                $token = explode('.', explode('userToken:', $key)[1])[0];
                $user = helper('get_user_from_token', $token);
                if($user == NULL)
                    ClearCache::dispatch($key);
                else if($record->id == $user->id);
                    ClearCache::dispatch($key);
            }
            
        if($record->id == PUBLIC_USER_ID)
            ClearCache::dispatch('publicUser');
    }
    
    private function clearSettingCache($record)
    {
        ClearCache::dispatch('settings');
    }
    
    private function clearColumnSetsOrArraysCache($table)
    {
        ClearCache::dispatch('allAuths');
        
        $ts = ['column_sets', 'column_arrays'];        
        foreach($ts as $t)
        {
            $temp = DB::table($t)->where('table_id', $table->id)->get();
            foreach($temp as $rec)
            {
                $key = 'table:'.$table->name.'|type:'.$t.'|id:'.$rec->id; 
                ClearCache::dispatch($key);
            }
        }
    }
    
    private function clearRecordCache($tableName, $data)
    {
        foreach($data as $columnName => $value)
        {
            if(is_array($value)) $value = json_encode($value);
            
            $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:BaseModel';
            ClearCache::dispatch($cacheKey);
            
            $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:*';
            ClearCache::dispatch($cacheKey);
            
            $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|relationData';
            ClearCache::dispatch($cacheKey);
            
            foreach($data as $returnColumnName => $v)
            {
                $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:'.$returnColumnName;
                ClearCache::dispatch($cacheKey);
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
        ClearCache::dispatch('allAuths');
        ClearCache::dispatch('tableName:'.$table->name.'|tableInfo');
        
        $this->clearTablesAndColumnCommonCache($table);
    }
    
    private function clearTableStandartCache($tableId, $tableName)
    {
        ClearCache::dispatch('tableName:'.$tableName.'|fillableColumns');
        ClearCache::dispatch('tableName:'.$tableName.'|castsColumns');
        ClearCache::dispatch('tableName:'.$tableName.'|allColumsFromDb');
        ClearCache::dispatch('tableName:'.$tableName.'|allColumsFromDbWithTableAliasAndGuiType');
        
        if(strstr($tableName, '_archive')) return;
        
        $key = 'table:'.$tableName.'|type:column_arrays|id:';
        $columnArrays = DB::table('column_arrays')->where('table_id', $tableId)->get();
        foreach($columnArrays as $columnArray) 
        {
            ClearCache::dispatch($key.$columnArray->id);
            $this->clearRecordCache('column_arrays', $columnArray);
        }
        ClearCache::dispatch($key.'0');
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
                    ClearCache::dispatch($key);
                }
            }
            else if(strstr($key, 'userToken:'))
            {
                //userToken:1111111111111111d1.tableName:test.mapFilters
                $temp = explode('.', explode('tableName:', $key)[1])[0];
                if($temp == $table->name)
                    ClearCache::dispatch($key);
                else
                {
                    if($table->name == $temp) 
                    {
                        ClearCache::dispatch($key);
                        return;
                    }
                    
                    $customLayers = DB::table('custom_layers')->where('table_id', $table->id)->pluck('name', 'table_id');
                    foreach($customLayers as $tableId => $customLayer)
                        if(helper('seo', $customLayer) == $temp)
                        {
                            ClearCache::dispatch($key);
                            return;
                        }
                }
            }
            else if(substr($key, -16, 16) == '|tableGroups')
            {
                ClearCache::dispatch($key);
            }
            else if(strstr($key, 'customLayerSeoName:'))
            {
                ClearCache::dispatch($key);
            }
        }
    }
}