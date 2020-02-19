<?php

namespace App\BaseModelTraits;

trait BaseModelWhereNumericTrait 
{
    public function addWhereForNumericBasicFilter($params, $operator, $data)
    {
        /*if(strlen($params->column->table_alias) > 0) 
            $params->column_name = $params->column->table_alias . '.' . $params->column->name;*/

        $params->model->where($params->column_name_with_alias, $operator, $data);
    }
    
    public function addWhereForNumeric($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForNumericBasicFilter($params, '=', $params->filter->filter);
            case 2: return $this->addWhereForNumericBasicFilter($params, '!=', $params->filter->filter);
            case 3: return $this->addWhereForNumericBasicFilter($params, '<', $params->filter->filter);
            case 4: return $this->addWhereForNumericBasicFilter($params, '>', $params->filter->filter);
            default: dd('numeric için filtre işlemi yok');
        }       
    }
}