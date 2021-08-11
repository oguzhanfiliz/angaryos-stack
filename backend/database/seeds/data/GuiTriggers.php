<?php

use App\BaseModel;

$column_gui_triggers = [];
$column_gui_triggers['name'] =
[
    [
        'name' => 'autoFillNameColumnFromDisplayNameColumn',
        'display_name' => 'Ad kolonu otomatik doldur',
        'control_column_ids' => [28],//'display_name, Tablo yada kolon eklenme sıralaması değişirse yeni id yazılması gerekir
    ]
];

foreach($column_gui_triggers as $columnName => $triggers)
    foreach($triggers as $i => $trigger)
    {
        $temp = $this->get_base_record();
        $temp = array_merge($trigger, $temp);

        $column_gui_triggers[$columnName][$i] = new BaseModel('column_gui_triggers', $temp);
        $column_gui_triggers[$columnName][$i]->save();
    }