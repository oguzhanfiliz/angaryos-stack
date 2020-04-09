<?php

namespace App\Listeners;

class TableSubscriber 
{
    use TableSubscriberTrait;
    
    public function listRequested($model, $params) 
    {
        return $this->getDataForList($model, $params);
    }
    
    public function createRequested($model, $params) 
    {
        return $this->getDataForCreate($model, $params);
    }
    
    public function editRequested($model, $params) 
    {
        return $this->getDataForEdit($model, $params);
    }
    
    public function showRequested($model, $params) 
    {
        return $this->getDataForShow($model, $params);
    }
    
    public function selectColumnDataRequested($column, $params) 
    {
        return $column->getSelectColumnData($params);
    }
    
    public function realtionTableDataRequested($record, $params)
    {
        return $record->getRelationTableDataForInfo($params);
    }
    
    public function storeRequested($params)
    {
        return $this->createNewRecord($params->request, $params->table->name);
    }
    
    public function updateRequested($params, $record)
    {
        return $this->updateRecord($record, $params->request);
    }
    
    public function deleteRequested($record)
    {
        return $this->deleteRecord($record);
    }
    
    public function cloneRequested($dataArray)
    {
        foreach($dataArray as $key => $value)
            if(is_array($value))
                $dataArray[$key] = json_encode($value);
           
        foreach(array_keys($dataArray) as $columnName)
        {
            $vals = get_attr_from_cache('columns', 'name', $columnName, 'column_validation_ids');
            if(strlen($vals) == 0) continue;
            
            $vals = json_decode($vals);
            foreach($vals as $val)
            {
                $temp = get_attr_from_cache('column_validations', 'id', $val, 'validation_with_params');
                $temp = explode(':', $temp)[0];
                
                if($temp == 'unique')
                {
                    $dbTypeId = get_attr_from_cache('columns', 'name', $columnName, 'column_db_type_id');
                    $dbTypeName = get_attr_from_cache('column_db_types', 'id', $dbTypeId, 'name');
                    
                    switch($dbTypeName)
                    {
                        case 'string':
                        case 'text':
                            $dataArray[$columnName] = $dataArray[$columnName] . 'klon';
                            break;
                        case 'integer':
                            $dataArray[$columnName] = $dataArray[$columnName] + 1000;
                            break;
                        default:
                            custom_abort('db.type.'.$dbTypeName.'.not.clonable');
                    }
                    
                }
            }
        }
        
        $errors = $this->validateRecordData($dataArray);
        if($errors != []) return $errors;
        
        return $this->createNewRecord($dataArray);
    }
    
    public function archiveRequested($record, $params)
    {
        return $this->getDataForArchive($record, $params);
    }
    
    public function deletedRequested($params)
    {
        return $this->getDataForDeleted($params);
    }
    
    public function restoreRequested($archiveRecord)
    {
        $tableName = substr($archiveRecord->getTable(), 0, -8);
        $record = get_attr_from_cache($tableName, 'id', $archiveRecord->record_id, '*');
        
        return $this->restoreRecord($archiveRecord, $record);
    }
    
    public function storeSuccess($params, $record)
    {
        return $this->getDataForSelectElement($params, $record);
    }
    
    public function updateSuccess($params, $orj, $record)
    {
        return $this->getDataForSelectElement($params, $record);
    }
    
    public function authAssignRequested($params)
    {
        $this->authAssign($params);
    }
    
    public function quickSearch($model, $params, $words)
    {
        return $this->getDataForQuickSearch($model, $params, $words);
    }
}
