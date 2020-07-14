<?php

namespace App\BaseModelTraits;

trait BaseModelWhereDateTimeTrait 
{
    public function addWhereForDateTimeBasicFilter($params, $operator)
    {
        $params->model->where($params->column_name_with_alias, $operator, $params->filter->filter);
    }
    
    public function addWhereForDateTimeBetweenFilter($params)
    {
        $params->model->whereBetween($params->column_name_with_alias, [$params->filter->filter, $params->filter->filter2]);
    }
    
    public function addWhereForDateTime($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForDateTimeBasicFilter($params, '=');
            case 2: return $this->addWhereForDateTimeBasicFilter($params, '<');
            case 3: return $this->addWhereForDateTimeBasicFilter($params, '>');
            case 4: return $this->addWhereForDateTimeBetweenFilter($params);
            default: dd('date time için filtre işlemi yok: ' . $params->filter->type);
        }       
    }
}