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
                $column->fillVariables();
                $tempTriggers = [];
                foreach($column->getRelationData('column_gui_trigger_ids') as $guiTrigger)
                {
                    $guiTrigger->fillVariables();
                    $controlColumns = $guiTrigger->getRelationData('control_column_ids');
                    foreach($controlColumns as $control)
                    {
                        $controlName = $control->name;
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