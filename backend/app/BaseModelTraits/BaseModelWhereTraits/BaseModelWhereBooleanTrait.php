<?php

namespace App\BaseModelTraits;

trait BaseModelWhereBooleanTrait 
{
    public function addWhereForBooleanBasicFilter($params)
    {
        /*if(strlen($params->column->table_alias) > 0) 
            $params->column_name = $params->column->table_alias . '.' . $params->column_name;*/

        $params->model->where($params->column_name_with_alias, $params->filter->filter);
    }
    
    public function addWhereForBoolean($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForBooleanBasicFilter($params);
            default: dd('date time için filtre işlemi yok');
        }       
    }
}