<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;


trait BaseModelGetDataGuiTriggersTrait 
{    
    public function getGuiTriggers($columns)
    {
        $guiTriggers = [];
        foreach($columns as $i => $column)
            if(strlen($column->column_gui_trigger_ids) > 0)
            {
                $tempTriggers = [];
                
                $ids = json_decode($column->column_gui_trigger_ids);
                foreach($ids as $guiTriggerId)
                {
                    $guiTrigger = get_attr_from_cache('column_gui_triggers', 'id', $guiTriggerId, '*');
                    
                    $controlColumnIds = json_decode($guiTrigger->control_column_ids);
                    foreach($controlColumnIds as $controlId)
                    {
                        $controlName = get_attr_from_cache('columns', 'id', $controlId, 'name');
                        if($controlName == 'own')
                            $controlName = $column->name;
                        
                        if(!isset($guiTriggers[$controlName]))
                            $guiTriggers[$controlName] = [];
                        
                        array_push($guiTriggers[$controlName], $guiTrigger->name);
                    }
                }
            }
            
        return $guiTriggers;
    }
    
}