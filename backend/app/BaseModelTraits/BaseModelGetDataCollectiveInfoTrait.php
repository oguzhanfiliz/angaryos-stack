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
                $collectiveInfo = $column->getRelationData('column_collective_info_id');
                $infos[$column->name] =
                [
                    'type' => $collectiveInfo->name,
                    'data' => $model->{$collectiveInfo->name}($column->table_alias.'.id')
                ];
            }
            
        return $infos;
    }
    
}