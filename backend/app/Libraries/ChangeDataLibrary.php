<?php

namespace App\Libraries;

class ChangeDataLibrary 
{
    public function UpdateData($columns, $data, $record) 
    {
        $geoColumns = ['point', 'multipoint', 'linestring', 'multilinestring', 'polygon', 'multipolygon'];
                
        foreach($data as $key => $value)
        {
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
        $sourceColumn = $params->relation->getRelationData('relation_source_column_id');
        $type = $sourceColumn->getRelationData('column_db_type_id');
        
        $value = [];
        switch($type->name)
        {
            case 'integer':
                foreach($params->value as $item)
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