<?php

namespace App\Policies;

use App\BaseModel;
use App\User;

trait UserPolicyTrait
{    
    private function columnSetIsHaveSingleColumn($tableName, $columnSetId, $singleColumnName)
    {
        if($columnSetId == 0)
        {
            $columnIds = get_attr_from_cache('tables', 'name', $tableName, 'column_ids');
            if($columnIds == NULL) return FALSE;
            $columnIds = json_decode($columnIds);
        }
        else
        {
            $columnArrayIds = get_attr_from_cache('column_sets', 'id', $columnSetId, 'column_array_ids');
            $columnArrayIds = json_decode($columnArrayIds);
            
            $columnIds = [];
            foreach($columnArrayIds as $columnArrayId)
            {
                $columnArray = get_attr_from_cache('column_arrays', 'id', $columnArrayId, '*');
                if($columnArray == NULL) return FALSE;
                
                $columnArrayTypeName = get_attr_from_cache('column_array_types', 'id', $columnArray->column_array_type_id, 'name');
                if($columnArrayTypeName != 'direct_data') continue;
                
                $temp = json_decode($columnArray->column_ids);
                $columnIds = array_merge($columnIds, $temp);
            }
        }
        
        $singleColumnId = get_attr_from_cache('columns', 'name', $singleColumnName, 'id');
        return in_array($singleColumnId, $columnIds);
    }
    
    public function columnIsPermittedForQuery(User $user, $column_name)
    {
        $control = $this->columnIsPermittedForList($user, $column_name);
        if($control) return TRUE;
        
        $table = 'column_arrays';
        return $this->columnIsPermited($user, $column_name, $table, 'queries');
    }
    
    public function columnIsPermittedForList(User $user, $column_name)
    {
        $table = 'column_arrays';
        return $this->columnIsPermited($user, $column_name, $table, 'lists');
    }

    private function columnIsPermited($user, $columnName, $table, $type)
    {
        global $pipe;
        
        if(!isset($user->auths["tables"])) return FALSE;
        if(!isset($user->auths["tables"][$pipe['table']])) return FALSE;
        if(!isset($user->auths["tables"][$pipe['table']][$type])) return FALSE;
        
        foreach($user->auths["tables"][$pipe['table']][$type] as $columnArrayOrSetId)
        {
            if($columnArrayOrSetId == 0)
                $control = $this->columnIsPermitedForAllColumns($columnName);
            else
                $control = $this->columnIsPermitedForColumnArrayOrSet($columnName, $table, $columnArrayOrSetId);
            
            if($control) return TRUE;
        }
        
        return FALSE;
    }
    
    private function columnIsPermitedForAllColumns($columnName)
    {
        global $pipe;
        
        $model = new BaseModel($pipe['table']);
        foreach($model->getAllColumnsFromDB() as $column)
            if($column['name'] == $columnName)
                return TRUE;
            
        return FALSE;
    }
    
    private function columnIsPermitedForColumnArrayOrSet($columnName, $table, $columnArrayOrSetId)
    {
        //$model = new BaseModel($table);
        //$model = $model->find($columnArrayOrSetId);

        $model = get_attr_from_cache($table, 'id', $columnArrayOrSetId, '*');
        
        $arr = helper('divide_select', $model->join_columns);
        foreach($arr as $c)
        {
            if(strlen(trim($c)) == 0) continue;
            
            $temp = helper('get_column_data_for_joined_column', $c);
            if($temp[1] == $columnName)
                return TRUE;
        }

        $columnIds = json_decode($model->column_ids);
        //foreach($model->getRelationData('column_ids') as $column)
        foreach($columnIds as $columnId)
        {
            $tempColumnName = get_attr_from_cache('columns', 'id', $columnId, 'name');
            if($tempColumnName == $columnName)
                    return TRUE;
        }
            
        return FALSE;
    }
    
    public function treeIsPermittedForRelationTableData(User $user, $tree)
    {
        $tree = explode(':', $tree);
        if(count($tree) != 2) return FALSE;
        
        global $pipe;
        
        if(!in_array($tree[0], $user->auths['tables'][$pipe['table']]['shows']))
            return FALSE;
        
        $column_set = get_attr_from_cache('column_sets', 'id', $tree[0], '*');
                        
        /*if(!in_array($tree[1], $column_set->column_group_ids))
            return FALSE;
        
        $column_group = get_attr_from_cache('column_groups', 'id', $tree[1], '*');
        $column_group->fillVariables();

        if(!in_array($tree[2], $column_group->column_array_ids))
            return FALSE;
        
        $column_array = get_attr_from_cache('column_arrays', 'id', $tree[2], '*');
        $column_array->fillVariables();         */
        
        $ids = json_decode($column_set->column_array_ids);
        if(!in_array($tree[1], $ids))
            return FALSE;
        
        $column_array = get_attr_from_cache('column_arrays', 'id', $tree[1], '*');
        $typeName = get_attr_from_cache('column_array_types', 'id', $column_array->column_array_type_id, 'name');
        
        if($typeName != 'table_from_data')
            return FALSE;
        
        return TRUE;
    }
    
    public function columnSetOrArrayIsPermitted($user, $columnSetOrArrayId, $type)
    {
        if(!is_numeric($columnSetOrArrayId)) return FALSE;
        
        global $pipe;
        
        if(!isset($user->auths['tables'])) return FALSE;
        if(!isset($user->auths['tables'][$pipe['table']])) return FALSE;
        if(!isset($user->auths['tables'][$pipe['table']][$type])) return FALSE;
        
        if(!in_array((int)$columnSetOrArrayId, $user->auths['tables'][$pipe['table']][$type]))
            return FALSE;
        
        return TRUE;
    }
    
    public function recordPermitted($record, $type)
    {
        switch ($type) 
        {
            case 'delete':
                $permitName = '_is_deletable';
                break;
            case 'clone':
                $permitName = '_is_showable';
                break;
            case 'restore':
                $permitName = '_is_restorable';
                break;
            case 'export':
                $permitName = '_is_exportable';
                break;
            case 'update':
                $permitName = '_is_editable';
                break;
            default: dd('invalid type: ' . $type);
        }
        
        $permissions = $this->getRecordPermissions($record);
        
        if(isset($permissions->{$permitName}) && !$permissions->{$permitName}) return FALSE;
        return TRUE;
    }
    
    private function getRecordPermissions($record)
    {
        if(get_class($record) == 'stdClass')
        {
            global $pipe;
            $model = new BaseModel($pipe['table']);
            $record = $model->find($record->id);
        }
        
        $model = $record->getQuery();
        $record->addFilters($model, $record->getTable());
        $model->where($record->getTable().'.id', $record->id);
        
        return $model->first();
    }
}
