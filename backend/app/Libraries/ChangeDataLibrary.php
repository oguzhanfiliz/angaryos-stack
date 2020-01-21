<?php

namespace App\Libraries;

class ChangeDataLibrary 
{
    public function UpdateData($columns, $data, $record) 
    {
        $geoColumns = ['point', 'multipoint', 'linestring', 'multilinestring', 'polygon', 'multipolygon'];

        foreach($data as $key => $value)
        {
            if($key == 'single_column_name') continue;
            if(!isset($columns[$key])) continue;
                
            if($columns[$key]['type'] == 'jsonb')
            {
                $params = helper('get_null_object');
                $params->value = json_decode ($value);

                $column = get_attr_from_cache('columns', 'name', $key, '*');

                $value = ColumnClassificationLibrary::relation($this, '', $column, NULL, $params);
            }

            $record->{$key} = $value;
        } 
        
        return $record;
    }

    public function ForTableIdAndColumnIds($params)
    {
        $sourceColumnDbTypeId = get_attr_from_cache('columns', 'id', $params->relation->relation_source_column_id, 'column_db_type_id');
                
        $typeName = get_attr_from_cache('column_db_types', 'id', $sourceColumnDbTypeId, 'name');
        $value = [];
        switch($typeName)
        {
            case 'integer':
                if(is_array($params))
                    $temp = $params['value'];
                else
                    $temp = $params->value;
                
                //if(!is_array($temp))dd($params);
                
                if($temp != NULL)
                    foreach($temp as $item)
                        array_push($value, (int)$item);
                
                if($value == []) $value = NULL;
                
                break;
            default:
                dd('updateDataForJsonbColumnForTableIdAndColumnIds');
        }
        
        return $value;
    }
    
    public function ForDataSource($params)
    {
        return $params->value;
    }
    
    public function ForBasicColumn($params)
    {
        return $params->value;
    }
}