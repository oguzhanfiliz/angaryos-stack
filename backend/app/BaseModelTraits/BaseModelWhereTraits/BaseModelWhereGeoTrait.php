<?php

namespace App\BaseModelTraits;

trait BaseModelWhereGeoTrait 
{
    public function addWhereForGeoBasicFilter($params, $whereType)
    {
        $params->model->where(function ($query) use($params, $whereType)
        {
            $columnSrid = helper('get_column_srid', 
            [
                'table' => $this->getTable(),
                'column' => $params->column->name
            ]);
            
            $filters = json_decode($params->filter->filter);
            foreach($filters as $filter)
            {
                $where = "ST_GeomFromText('$filter', ".\Auth::user()->srid.")";
                $where = "ST_Transform($where, $columnSrid)";
                $where = "ST_Intersects($params->column_name_with_alias, $where)";
                $query->{$whereType}($where);
            }
        });
    }
    
    public function addWhereForGeo($params)
    {
        switch($params->filter->type)
        {
            case 1: return $this->addWhereForGeoBasicFilter($params, 'orWhereRaw');
            case 2: return $this->addWhereForGeoBasicFilter($params, 'whereRaw');
            default: dd('geo için filtre işlemi yok');
        }       
    }
}