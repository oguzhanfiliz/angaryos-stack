<?php
use App\BaseModel;

$temps = 
[
    'data_sources' => 
    [
        [
            'name' => 'Kübis DB',
            'data_source_type_id' => 1,
            'host' =>  '192.168.10.151',
            'user_name' => 'omap',
            'passw' => 'Kozidbim.2643*',
            'params' => 'postgres'
        ]
    ]
];

$baseRecord = $this->get_base_record();

foreach($temps as $tableName => $records)
{
    foreach($records as $record)
    {
        $temp = array_merge($baseRecord, $record);
        $temp = new BaseModel($tableName, $temp);
        $temp->save();
    }
}