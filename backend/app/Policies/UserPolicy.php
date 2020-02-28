<?php

namespace App\Policies;

use App\User;
use App\BaseModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    use UserPolicyTrait;

    public function viewAny($user, $params)
    {
        global $pipe;
        
        if(!isset($user->auths['tables'][$pipe['table']]['lists'])) 
            return FALSE;
            
        if(!in_array($params->column_array_id, $user->auths['tables'][$pipe['table']]['lists']))
            return FALSE;
        
        return TRUE;
    }

    //show için model oluşturulurken filtrelerde onun içine gömüldüğü için burda özel olarak kontrol ediliyor 
    //recordPermitted ile kontrol edilse bir db sorgusu daha yapmak gerekecek
    public function view($user, $record)
    {
        if($record == NULL) return FALSE;
        if(isset($record->_is_showable) && !$record->_is_showable) return FALSE;
        
        return TRUE;
    }

    public function create($user, $columnSetId)
    {
        return $this->columnSetOrArrayIsPermitted($user, $columnSetId, 'creates');
    }

    public function update($user, $record, $columnSetId, $singleColumnName = NULL)
    {
        $control = $this->columnSetOrArrayIsPermitted($user, $columnSetId, 'edits');
        if(!$control) return FALSE;
        
        if($singleColumnName != NULL)
        {
            $control = $this->columnSetIsHaveSingleColumn($record->getTable(), $columnSetId, $singleColumnName);
            if(!$control) return FALSE;
        }
        
        return $this->recordPermitted($record, __FUNCTION__);
    }

    public function delete($user, $record)
    {
        if(!isset($user->auths['tables'][$record->getTable()]['delete'])) 
            return FALSE;
        
        return $this->recordPermitted($record, __FUNCTION__);
    }
    
    public function cloneRecord($user, $record)
    {
        global $pipe;
        
        if(!isset($user->auths['tables'][$pipe['table']]['creates'])) return FALSE;
        
        return $this->recordPermitted($record, 'clone');
    }
    
    public function archive($user, $record, $params)
    {
        $tableName = substr($params->table_name, 0, -8);
        
        if(!isset($user->auths['tables'][$tableName]['restore'])) 
            return FALSE;
        
        $control = $this->columnSetOrArrayIsPermitted($user, $params->column_array_id, 'lists');
        if(!$control) return FALSE;
        
        return $this->recordPermitted($record, 'restore');
    }

    public function restore($user, $archiveRecord)
    {
        $tableName = substr($archiveRecord->getTable(), 0, -8);
        
        if(!isset($user->auths['tables'][$tableName]['restore'])) 
            return FALSE;
        
        $record = get_attr_from_cache($tableName, 'id', $archiveRecord->record_id, '*');
        
        if($record == NULL)
        {
            $tableName = substr($archiveRecord->getTable(), 0, -8);
            return count($user->auths['tables'][$tableName]['deleteds']) > 0;
        }
        
        return $this->recordPermitted($record, 'restore');
    }
    
    public function restored($user, $record)
    {
        $tableName = $record->getTable();
        
        $auths = $user->auths;
        
        if(!isset($auths['filters'])) return TRUE;
        if(!isset($auths['filters'][$tableName])) return TRUE;
        if(!isset($auths['filters'][$tableName]['list'])) return TRUE;
        
        $filters = $auths['filters'][$tableName]['list'];
        
        $model = $record->getQuery(); 
        $model->whereRaw($tableName.'.id = '.$record->id);
        foreach($filters as $filterId)
        {
            $sqlCode = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');
            $sql = str_replace('TABLE', $tableName, $sqlCode);            
            $model->whereRaw($sql);
        }
        return (count($model->get()) > 0);
    }
    
    public function deleted($user, $params)
    {
        $tableName = substr($params->table_name, 0, -8);
        $columnArrayId = $params->column_array_id;
        if(!is_numeric($columnArrayId)) return FALSE;
        
        if(!isset($user->auths['tables'])) return FALSE;
        if(!isset($user->auths['tables'][$tableName])) return FALSE;
        if(!isset($user->auths['tables'][$tableName]['deleteds'])) return FALSE;
        
        if(!in_array((int)$columnArrayId, $user->auths['tables'][$tableName]['deleteds']))
            return FALSE;
        
        return TRUE;
    }

    public function forceDelete(User $user, User $model)
    {
        dd(1111111116);
    }
    
    public function assignAuth($user)
    {
        return isset($user->auths['admin']['authWizard']);
    }
    
    public function missionTrigger($user, $mission)
    {
        return isset($user->auths['missions'][$mission->id]);
    }
    
    public function dashboardGetData($user, $auth)
    {
        $auth = explode(':', $auth);
        return isset($user->auths[$auth[0]][$auth[1]][$auth[2]][$auth[3]]);
    }
}
