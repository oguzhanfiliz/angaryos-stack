<?php

namespace App\Http\Controllers\Api\V1;

use DB;

Trait ColumnGuiTriggerTrait
{
    /**** silinecek. bu alan unique name içine alınacak   ****/
    private function nameMustBeUnique($table, $column, $params)
    {
        $temp = DB::table($table->getTable())->where('name', '=', $params->name)->get();
        if(count($temp) == 0) return ['message' => 'OK'];
        return ['danger' => 'Aynı isimde başka ayar var!'];
    }
}
