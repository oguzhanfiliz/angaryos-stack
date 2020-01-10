<?php

namespace App\Listeners;

class TableAfterTriggerSubscriber 
{
    private function triggerSubscriber($record, $table, $column, $subscriber, $type)
    {
        if($column != NULL) $value = $record->{$column->name};
        $user = \Auth::user();
        
        $return = NULL;
        eval(helper('clear_php_code', $subscriber->php_code));          
        return $return == NULL ? [] : $return;
    }
    
    private function controlSubscribers($record, $table, $columns, $type)
    {
        $returned = [];
        
        if(is_array($table->subscriber_ids) || strlen($table->subscriber_ids) > 0)
        {
            $table->fillVariables();
            $subscribers = $table->getRelationData('subscriber_ids');
            foreach($subscribers as $subscriber)
            {
                $subscriberType = $subscriber->getRelationData('subscriber_type_id');
                if($subscriberType->name != 'after') continue;

                $temp = $this->triggerSubscriber($record, $table, NULL, $subscriber, $type);
                $returned = array_merge($returned, $temp);
            }
        }
        
        foreach($columns as $column)
        {
            if(is_array($column->subscriber_ids) || strlen($column->subscriber_ids) > 0)
            {
                $column->fillVariables();
                $subscribers = $column->getRelationData('subscriber_ids');
                foreach($subscribers as $subscriber)
                {
                    $subscriberType = $subscriber->getRelationData('subscriber_type_id');
                    if($subscriberType->name != 'after') continue;
                    
                    $temp = $this->triggerSubscriber($record, $table, $column, $subscriber, $type);
                    $returned = array_merge($returned, $temp);
                }
            }
        }
                
        if(count($returned) > 0)
        {
            //after içinde gelen data ile kayır update edilebilir.
            dd('controlSubscribers after');
            dd($returned);
            global $pipe;
            $pipe['overrideRequestDatas'] = $returned;
        }
        
        return TRUE;
    }
    
    public function storeSuccess($params, $record)
    {
        return $this->controlSubscribers($record, $params->table, $params->columns, 'create');
    }
    
    public function updateSuccess($params, $orj, $record)
    {
        return $this->controlSubscribers($record, $params->table, $params->columns, 'update');
    }
    
    public function deleteSuccess($record)
    {
        $table = get_attr_from_cache('tables', 'name', $record->getTable(), '*');
        
        $columns = [];
        foreach($record->toArray() as $columnName => $data)
        {
            $column = get_attr_from_cache('columns', 'name', $columnName, '*');
            array_push($columns, $column);
        }
        
        return $this->controlSubscribers($record, $table, $columns, 'delete');
    }
    
    public function cloneSuccess($cloneRecord)
    {
        $table = get_attr_from_cache('tables', 'name', $cloneRecord->getTable(), '*');
        
        $columns = [];
        foreach($cloneRecord->toArray() as $columnName => $data)
        {
            $column = get_attr_from_cache('columns', 'name', $columnName, '*');
            array_push($columns, $column);
        }
        
        return $this->controlSubscribers($cloneRecord, $table, $columns, 'clone');
    }
    
    public function restoreSuccess($archiveRecord, $record)
    {
        $tableName = $record->getTable();
        $table = get_attr_from_cache('tables', 'name', $tableName, '*');
        
        $columns = [];
        foreach($record->toArray() as $columnName => $value)
        {
            $column = get_attr_from_cache('columns', 'name', $columnName, '*');
            array_push($columns, $column);
        }
        
        return $this->controlSubscribers($archiveRecord, $table, $columns, 'restore');
    }
}
