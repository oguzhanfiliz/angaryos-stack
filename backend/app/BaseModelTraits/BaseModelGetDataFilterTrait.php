<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;

use App\BaseModel;
use Auth;
use DB;


trait BaseModelGetDataFilterTrait 
{    
    public function addFilters($model, $tableName)
    {
        $auths = Auth::user()->auths;
        
        if(!isset($auths['filters'])) return;
        if(!isset($auths['filters'][$tableName])) return;
        
        $auths = $auths['filters'][$tableName];
        
        foreach($auths as $type => $filters)
            if($filters != [])
                $this->{'add'. helper('str_to_class_name', $type) .'Filters'}($model, $filters, $tableName);
    }
        
    private function addListFilters($model, $filters, $tableName)
    {
        foreach($filters as $filterId)
        {
            $sqlCode = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');
            $sql = str_replace('TABLE', $tableName, $sqlCode);            
            $model->whereRaw($sql);      
        }
    }
    
    private function addUpdateFilters($model, $filters, $tableName)
    {
        $this->addSelectForFilters($model, $filters, $tableName, 'is_editable');
    }
    
    private function addDeleteFilters($model, $filters, $tableName)
    {
        $this->addSelectForFilters($model, $filters, $tableName, 'is_deletable');
    }
    
    private function addRestoreFilters($model, $filters, $tableName)
    {
        $this->addSelectForFilters($model, $filters, $tableName, 'is_restorable');
    }
    
    private function addShowFilters($model, $filters, $tableName)
    {
        $this->addSelectForFilters($model, $filters, $tableName, 'is_showable');
    }
    
    private function addExportFilters($model, $filters, $tableName)
    {
        $this->addSelectForFilters($model, $filters, $tableName, 'is_exportable');
    }
    
    private function addSelectForFilters($model, $filters, $tableName, $alias)
    {
        $filterSqls = [];
        foreach($filters as $filterId)
        {
            $sqlCode = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');
            $sql = str_replace('TABLE', $tableName, $sqlCode);  
            
            array_push($filterSqls, $sql);
        }
        
        $sql = implode(' and ', $filterSqls);
        $sql = "($sql) as _" . $alias;
        
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
}