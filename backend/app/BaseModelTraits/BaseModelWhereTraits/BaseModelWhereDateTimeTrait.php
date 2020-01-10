<?php

namespace App\BaseModelTraits;

trait BaseModelWhereDateTimeTrait 
{
    public function addWhereForDateTimeBasicFilter($params, $operator)
    {
        /*if(strlen($params->column->table_alias) > 0) 
            $params->column_name = $params->column->table_alias . '.' . $params->column_name;*/

        $params->model->where($params->column_name_with_alias, $operator, $params->filter->filter);
    }
    
    public function addWhereForDateTime($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForDateTimeBasicFilter($params, '=');
            case 2: return $this->addWhereForDateTimeBasicFilter($params, '<');
            case 3: return $this->addWhereForDateTimeBasicFilter($params, '>');
            default: dd('date time için filtre işlemi yok: ' . $params->filter->type);
        }       
    }
}