<?php

namespace App\Listeners;

use App\BaseModel;
use Cache;
use DB;

class CacheSubscriber 
{
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
            case 'settings':
                $this->clearSettingCache($record);
                break;
            case 'column_sets':
                $this->clearColumnSetsCache($record);
                break;
            case 'users':
                $this->clearUserCache($record);
                break;
            
            case 'auth_groups':
                $this->clearAuthGroupsCache($record);
                break;
            
            case 'data_filters':
            case 'data_filter_types':
            case 'column_arrays':
            case 'column_sets':
                Cache::forget('allAuths');
                break;
        }
        
        return TRUE;
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
        return;
        //mesela column_db_type değiştirğinde columns->column_db_type_id__relation_data silinmeli
        dd('clearRelationDataCache');
        
        $json = $this->{$columnName};
        if(is_array($this->{$columnName})) $json = json_encode($this->{$columnName});
        $cacheKey = 'tableName:'.$this->getTable().'|id:'.$this->id.'|columnName:'.$columnName.'|columnData:'.$json.'.relationData';
    }
    
    private function clearUserCache($record)
    {
        Cache::forget('tableName:users|id:'.$record->id.'|authTree');
    }
    
    private function clearSettingCache($record)
    {
        Cache::forget('settings');
    }
    
    private function clearColumnSetsCache($record)
    {
        dd('clearColumnSetsCache');
        dd($record);
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
            
            $cacheKey = 'tableName:'.$tableName.'|id:'.$record->id.'|columnName:'.$columnName.'|columnData:'.$value.'.relationData';
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
    
    private function clearTableCache($table)
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
        
        //dd('clearTablesAndColumnCommonCache');
        //dd($table);
        /*$columns = $table->getRelationData('column_ids');
        $keys = array_keys(get_object_vars($columns));
        $json = json_encode($keys);
        
        $cacheKey = 'tableName:'.$this->getTable().'|columnNames:'.$json.'|form:'.$form.'|filteredColumns';
        Cache::forget($cacheKey);*/
    }
}
