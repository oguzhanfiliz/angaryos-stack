<?php

namespace App\BaseModelTraits;

trait BaseModelWhereIntegerTrait 
{
    public function addWhereForIntegerBasicFilter($params, $operator, $data)
    {
        /*if(strlen($params->column->table_alias) > 0) 
            $params->column_name = $params->column->table_alias . '.' . $params->column->name;*/

        $params->model->where($params->column_name_with_alias, $operator, $data);
    }
    
    public function addWhereForInteger($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForIntegerBasicFilter($params, '=', $params->filter->filter);
            case 2: return $this->addWhereForIntegerBasicFilter($params, '!=', $params->filter->filter);
            case 3: return $this->addWhereForIntegerBasicFilter($params, '<', $params->filter->filter);
            case 4: return $this->addWhereForIntegerBasicFilter($params, '>', $params->filter->filter);
            default: dd('integer için filtre işlemi yok');
        }       
    }
}