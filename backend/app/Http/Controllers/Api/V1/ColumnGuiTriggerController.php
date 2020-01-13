<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\User;
use App\BaseModel;

class ColumnGuiTriggerController extends Controller
{
    use ColumnGuiTriggerTrait;
    
    public function index(User $user, BaseModel $table, BaseModel $column, $triggerName)
    {
        send_log('info', 'Request Column Gui Trigger', [$triggerName, $table, $column]);
        
        if($table->getTable() != 'settings') custom_abort('invalid_params');
        
        $params = $this->getValidatedParams($column); 
        
        //$trigger = get_attr_from_cache('column_gui_triggers', 'name', $triggerName, '*');
        
        $data = $this->{$triggerName}($table, $column, $params);
        
        send_log('info', 'Response Column Gui Trigger', $data);
        
        return helper('response_success', $data);
    }
    
    private function getValidatedParams($column)
    {
        $params = read_from_response_data('get', 'params', TRUE);
        
        param_is_have($params, 'column_set_id');
        param_is_have($params, $column->name);
        
        return $params;
    }
}
