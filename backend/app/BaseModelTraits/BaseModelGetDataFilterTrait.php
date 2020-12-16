<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;

use App\BaseModel;
use Auth;
use DB;


trait BaseModelGetDataFilterTrait 
{    
    public function addFilters($model, $tableName, $filterType = 'all')
    {
        $user = Auth::user();
        $auths = $user->auths;
        
        if(!isset($auths['filters'])) return;
        if(!isset($auths['filters'][$tableName])) return;
        
        $auths = $auths['filters'][$tableName];
        
        foreach($auths as $type => $filters)
            if($filters != [])
                if($filterType == 'all' || $filterType == $type)
                    $this->{'add'. helper('str_to_class_name', $type) .'Filters'}($user, $model, $filters, $tableName);
    }
        
    private function addListFilters($user, $model, $filters, $tableName)
    {
        foreach($filters as $filterId)
        {
            $sql = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');   
            $sql = helper('reverse_clear_string_for_db', $sql); 
            $sql = $this->GetReplacedSql($sql, $tableName, $user);           
            $model->whereRaw($sql);      
        }
    }

    private function addSelectColumnDataFilters($user, $model, $filters, $tableName)
    {
        $this->addListFilters($user, $model, $filters, $tableName);
    }
    
    private function addUpdateFilters($user, $model, $filters, $tableName)
    {
        $this->addSelectForFilters($user, $model, $filters, $tableName, 'is_editable');
    }
    
    private function addDeleteFilters($user, $model, $filters, $tableName)
    {
        $this->addSelectForFilters($user, $model, $filters, $tableName, 'is_deletable');
    }
    
    private function addRestoreFilters($user, $model, $filters, $tableName)
    {
        $this->addSelectForFilters($user, $model, $filters, $tableName, 'is_restorable');
    }
    
    private function addShowFilters($user, $model, $filters, $tableName)
    {
        $this->addSelectForFilters($user, $model, $filters, $tableName, 'is_showable');
    }
    
    private function addExportFilters($user, $model, $filters, $tableName)
    {
        $this->addSelectForFilters($user, $model, $filters, $tableName, 'is_exportable');
    }
    
    private function addSelectForFilters($user, $model, $filters, $tableName, $alias)
    {
        $user = \Auth::user();

        $filterSqls = [];
        foreach($filters as $filterId)
        {
            $sql = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');  
            $sql = helper('reverse_clear_string_for_db', $sql); 
            $sql = $this->GetReplacedSql($sql, $tableName, $user);
            
            array_push($filterSqls, $sql);
        }
        
        $sql = implode(' and ', $filterSqls);
        $sql = "($sql) as _" . $alias;
        $sql = str_replace(' "', ' '.$tableName.'."', $sql);
        
        $model->addSelect(DB::raw($sql));
    }
    
    private function getFilterForRelationData($params)
    {
        $column_name = $params->joins[0]->connection_column_with_alias;        
        $column_name = explode(' as ', $column_name)[0];
        $column_name = explode('.', $column_name)[1];
        
        $data = $params->data->{$column_name};
        if(!is_array($data)) $data = [$data];
        
        return (Object)[
            'type' => 1,
            'filter' => $data
        ];
    }

    private function GetReplacedSql($sql, $tableName, $user)
    {
        $sql = str_replace('$record->', $tableName.'.', $sql);  

        foreach($user->toArray() as $key => $value)
            if(!is_array($value))
                $sql = str_replace('$user->'.$key, $value, $sql); 
        $sql = str_replace(' "', ' '.$tableName.'."', ' '.$sql);
        
        return $sql;
    }
}