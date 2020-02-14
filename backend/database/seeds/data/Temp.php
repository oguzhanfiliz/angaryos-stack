<?php
return;
use App\BaseModel;

$temps = 
[
    'data_sources' => 
    [
        [
            'name' => 'Kübis DB',
            'data_source_type_id' => 1,
            'host' =>  '192.168.10.151',
            'user_name' => 'postgres',
            'passw' => 'Kozidbim.2643*',
            'params' => 'cbs|public'
        ],
        /*[
            'name' => 'Ldap DB',
            'data_source_type_id' => 2,
            'host' =>  'ldap://192.168.1.82',
            'user_name' => 'cn=admin,dc=liderahenk,dc=org',
            'passw' => 'ssifre',
            'params' => 'dc=liderahenk,dc=org'
        ]*/
    ]
];

$u = App\User::find(1);
\Auth::login($u);

$baseRecord = $this->get_base_record();
foreach($temps as $tableName => $records)
{
    foreach($records as $record)
    {
        $temp = array_merge($baseRecord, $record);
        $temp = new BaseModel($tableName, $temp);
        $temp->save();
        
        echo 'Data Source insert OK';

        $lib = new \App\Libraries\DataSourceOperationsLibrary();
        $lib->TableEvent(
        [
            'type' => 'create', 
            'record' => $temp
        ]);

        echo 'Data Source Tables and Columns insert OK';
    }
}