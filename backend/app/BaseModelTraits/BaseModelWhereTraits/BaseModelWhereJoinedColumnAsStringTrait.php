<?php

namespace App\BaseModelTraits;

use DB;

trait BaseModelWhereJoinedColumnAsStringTrait 
{
    public function addWhereForJoinedColumnAsStringBasicFilter($params, $filter, $notLike = FALSE)
    {
        $temp = helper('get_column_data_for_joined_column', $params->column->select_raw);
        $temp[0] = helper('clear_aggregate_function_in_sql', $temp[0]);
        $params->model->where(DB::raw($temp[0]), ($notLike ? 'not ' : ''). 'ilike', $filter);
    }
    
    public function addWhereForJoinedColumnAsString($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, '%'.$params->filter->filter.'%');
            case 2: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, $params->filter->filter.'%');
            case 3: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, '%'.$params->filter->filter);
            case 4: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, $params->filter->filter);
            case 5: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, $params->filter->filter, TRUE);
            default: dd('joined string için filtre işlemi yok: ' . $params->filter->type);
        }       
    }
}