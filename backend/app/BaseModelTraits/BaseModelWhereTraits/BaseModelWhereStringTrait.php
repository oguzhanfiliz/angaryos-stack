<?php

namespace App\BaseModelTraits;

trait BaseModelWhereStringTrait 
{    
    public function addWhereForStringBasicFilter($params, $operator, $search)
    {
        $params->model->where($params->column_name_with_alias, $operator, $search);
    }

    public function addWhereForStringSpetialFilter($params, $search)
    {
        $obj = helper('json_str_to_object', $search);
        $where = $this->getWhereStringFromSpetialSearch($params->column_name_with_alias, $obj);
        $params->model->whereRaw($where);
    }

    private function getWhereStringFromSpetialSearch($columnWithAlias, $search)
    {
        $where = '( ';

        if(!isset($search->type)) custom_abort('uncorrect.spetial.params.for.string(type)');

        foreach($search->data as $item)
            $where .= $columnWithAlias.' ilike \'%'.$item.'%\' '.$search->type.' ';

        if(!isset($search->sub)) $where = substr($where, 0, -1 * (strlen($search->type)+2));
        else $where .= $this->getWhereStringFromSpetialSearch($columnWithAlias, $search->sub);

        $where .= ' )';
        return $where;
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
            case 6: 
                $this->addWhereForStringSpetialFilter($params, $params->filter->filter);
                break;
            default: dd('string için filtre işlemi yok');
        }       
    }
}