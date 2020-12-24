<?php

namespace App\BaseModelTraits;

use DB;

trait BaseModelWhereJoinedColumnAsStringTrait 
{
    public function addWhereForJoinedColumnAsString($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, '%'.$params->filter->filter.'%');
            case 2: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, $params->filter->filter.'%');
            case 3: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, '%'.$params->filter->filter);
            case 4: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, $params->filter->filter);
            case 5: return $this->addWhereForJoinedColumnAsStringBasicFilter($params, $params->filter->filter, TRUE);
            case 6: return $this->addWhereForJoinedColumnAsStringSpetialFilter($params, $params->filter->filter);
            default: dd('joined string için filtre işlemi yok: ' . $params->filter->type);
        }       
    }

    public function addWhereForJoinedColumnAsStringBasicFilter($params, $filter, $notLike = FALSE)
    {
        $params->column->select_raw = helper('reverse_clear_string_for_db', $params->column->select_raw);
        
        $temp = helper('get_column_data_for_joined_column', $params->column->select_raw);
        $temp[0] = helper('clear_aggregate_function_in_sql', $temp[0]);
                
        $params->model->where(DB::raw($temp[0]), ($notLike ? 'not ' : ''). 'ilike', $filter);
    }

    public function addWhereForJoinedColumnAsStringSpetialFilter($params, $search)
    {
        $colRaw = helper('reverse_clear_string_for_db', $params->column->select_raw);
        $colRaw = helper('clear_aggregate_function_in_sql', $colRaw);
        $colRaw = trim(explode(' as ', $colRaw)[0]);

        $obj = helper('json_str_to_object', $search);

        $where = $this->getWhereStringFromSpetialSearch($colRaw, $obj);

        $params->model->whereRaw($where);
    }
}