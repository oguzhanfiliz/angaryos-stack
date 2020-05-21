<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;


trait BaseModelGetDataCollectiveInfoTrait 
{    
    public function getCollectiveInfos($model, $columns)
    {
        $infos = [];
        foreach($columns as $column)
            if(strlen(@$column->column_collective_info_id) > 0)
            {
                $collectiveInfoName = get_attr_from_cache('column_collective_infos', 'id', $column->column_collective_info_id, 'name');
                
                $infos[$column->name] =
                [
                    'type' => $collectiveInfoName,
                    'data' => $model->{$collectiveInfoName}($column->table_alias.'.'.$column->name)
                ];
            }
            
        return $infos;
    }
    
}