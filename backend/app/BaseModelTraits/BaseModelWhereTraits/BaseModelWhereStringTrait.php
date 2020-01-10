<?php

namespace App\BaseModelTraits;

trait BaseModelWhereStringTrait 
{    
    public function addWhereForStringBasicFilter($params, $operator, $search)
    {
        $params->model->where($params->column_name_with_alias, $operator, $search);
    }
    
    public function addWhereForString($params)
    {
        switch($params->filter->type)
        {
            case 1: 
                $search = '%'.$params->filter->filter.'%';
                $this->addWhereForStringBasicFilter($params, 'ilike', $search);
                break;
            case 2: 
                $search = $params->filter->filter.'%';
                $this->addWhereForStringBasicFilter($params, 'ilike', $search);
                break;
            case 3: 
                $search = '%'.$params->filter->filter;
                $this->addWhereForStringBasicFilter($params, 'ilike', $search);
                break;
            case 4: 
                $this->addWhereForStringBasicFilter($params, 'ilike', $params->filter->filter);
                break;
            case 5: 
                $this->addWhereForStringBasicFilter($params, 'not ilike', $params->filter->filter);
                break;
            default: dd('string için filtre işlemi yok');
        }       
    }
}