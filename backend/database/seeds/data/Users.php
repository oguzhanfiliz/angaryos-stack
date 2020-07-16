<?php
use App\BaseModel;

unset($this->user->id);

$this->user->tc = '11111111111';
$this->user->name_basic = 'Ana';
$this->user->surname = 'Yönetici';
$this->user->email = 'iletisim@omersavas.com';
$this->user->password = \Hash::make('1234Aa.');
$this->user->location = 'POINT(498781.6902 4365044.2955)';
$this->user->srid = 4326;
$this->user->state = TRUE;
$this->user->user_id = 1;
$this->user->own_id = 1;
$this->user->department_id = $departments['Bilgi İşlem Müdürlüğü']->id;
$this->user->fillVariables();
$this->user->save();


$adminAuth = $this->get_base_record();
$adminAuth['name_basic'] = 'Yönetici yetkisi';
$adminAuth['auths'] = [];

$adminAuth = new BaseModel('auth_groups', $adminAuth);
$adminAuth->fillVariables();
$adminAuth->save();


$createExcept = [];//['data_filter_types'];
$hideMenuLink =
[
    'log_levels',
    'color_classes',
    'column_gui_triggers',
    'column_validations',
    'column_gui_types',
    'column_table_relations',
    'column_collective_infos',
    'column_db_types',
    'subscriber_types',
    'data_filter_types',
    'column_array_types',
    'column_set_types',
    'custom_layer_types',
    'data_source_col_relations',
    'data_source_types',
    'data_source_directions',
    'join_tables', 
];

$adminAuths = [];
foreach($tables as $table)
{
    $tableAuths = [];
    
    if(in_array($table->name, $hideMenuLink))
        array_push($tableAuths, 'tables:'.$table->name.':option:0');
    
    $temp = 'tables:'.$table->name.':';
    
    array_push($tableAuths, $temp.'delete:0');
    array_push($tableAuths, $temp.'restore:0');
    array_push($tableAuths, $temp.'export:0');
    
    if(isset($column_arrays[$table->name]))
    {
        foreach($column_arrays[$table->name] as $column_array)
        {
            array_push($tableAuths, $temp.'lists:'.$column_array->id);
            array_push($tableAuths, $temp.'queries:'.$column_array->id);
            array_push($tableAuths, $temp.'deleteds:'.$column_array->id);
        }
    }
    
    array_push($tableAuths, $temp.'lists:0');
    array_push($tableAuths, $temp.'queries:0');
        
        
    
    if(isset($column_sets[$table->name]))
    {
        foreach($column_sets[$table->name] as $column_set)
        {
            array_push($tableAuths, $temp.'shows:'.$column_set->id);
            array_push($tableAuths, $temp.'edits:'.$column_set->id);
            
            if(!in_array($table->name, $createExcept))
                array_push($tableAuths, $temp.'creates:'.$column_set->id);
        }
    }
    
    array_push($tableAuths, $temp.'shows:0');
    array_push($tableAuths, $temp.'edits:0');
    
    if(!in_array($table->name, $createExcept)) 
        array_push($tableAuths, $temp.'creates:0');
    
    array_push($tableAuths, $temp.'deleteds:0');
    
    
    
    $temp = 'filters:'.$table->name.':';
    
    if(isset($data_filters[$table->name]))
    {
        foreach($data_filters[$table->name] as $filter)
        {
            $type = $filter->getRelationData('data_filter_type_id')->name;
            array_push($tableAuths, $temp.$type.':'.$filter->id);
        }
    }
    
    if($table->name == 'users')
        array_push($tableAuths, 'tables:'.$table->name.':maps:0');
    
    $temp = $this->get_base_record();
    $temp['name_basic'] = $table_name_display_name_map[$table->name].' tüm yetki';
    $temp['auths'] = $tableAuths;
    
    $tableAuths = new BaseModel('auth_groups', $temp);
    $tableAuths->fillVariables();
    $tableAuths->save();
    
    array_push($adminAuths, $tableAuths->id);
}

$tempAuths = [];
foreach(DB::table('table_groups')->get() as $group)
    array_push($tempAuths, 'table_groups:0:0:'.$group->id);

$temp = $this->get_base_record();
$temp['name_basic'] = 'Admin tablo grupları (menü)';
$temp['auths'] = $tempAuths;

$tempAuths = new BaseModel('auth_groups', $temp);
$tempAuths->fillVariables();
$tempAuths->save();

array_push($adminAuths, $tempAuths->id);

array_push($adminAuths, 'admin:authWizard:0:0');
array_push($adminAuths, 'admin:userImitation:0:0');
array_push($adminAuths, 'admin:dataEntegrator:0:0');
array_push($adminAuths, 'admin:recordImport:0:0');



array_push($adminAuths, 'dashboards:RecordCount:auth_groups:0');
array_push($adminAuths, 'dashboards:RecordCount:users:0');
array_push($adminAuths, 'dashboards:RecordCount:departments:0');



array_push($adminAuths, 'dashboards:RefreshableNumber:JobCount:0');



array_push($adminAuths, 'dashboards:GraphicXY:Test:0');
array_push($adminAuths, 'dashboards:GraphicPie:Test:0');



array_push($adminAuths, 'map:0:0:0');
array_push($adminAuths, 'map:kmz:upload:0');



foreach($missions as $mission)
    array_push($adminAuths, 'missions:'.$mission->id.':0:0');



$adminAuth->auths = $adminAuths;
$adminAuth->save();

$this->user->auths = [$adminAuth->id];
$this->user->save();

$tokens = [];
$tokens[0] = 
[
    'token' => '1111111111111111d'.$this->user->id,
    'time' => strtotime(date('Y-m-d H:i:s'))
];
$this->user->tokens = $tokens;
$this->user->save();

$departments['Bilgi İşlem Müdürlüğü']->manager_id = $this->user->id;
$departments['Bilgi İşlem Müdürlüğü']->save();


unset($this->publicUser->id);
$this->publicUser->tc = '11111111112';
$this->publicUser->name_basic = 'Serbets';
$this->publicUser->surname = 'Kullanıcı';
$this->publicUser->email = 'info@omersavas.com';
$this->publicUser->password = \Hash::make('1234Aa.');
$this->publicUser->location = 'POINT(498781.6902 4365044.2955)';
$this->publicUser->srid = 4326;
$this->publicUser->state = TRUE;
$this->publicUser->user_id = 1;
$this->publicUser->own_id = 1;
$this->publicUser->department_id = $departments['Bilgi İşlem Müdürlüğü']->id;
$this->publicUser->fillVariables();
$this->publicUser->save();


unset($this->robotUser->id);
$this->robotUser->tc = '11111111113';
$this->robotUser->name_basic = 'Robot';
$this->robotUser->surname = 'Kullanıcı';
$this->robotUser->email = 'robot@omersavas.com';
$this->robotUser->password = \Hash::make('1234Aa.');
$this->robotUser->location = 'POINT(498781.6902 4365044.2955)';
$this->robotUser->srid = 4326;
$this->publicUser->state = TRUE;
$this->robotUser->user_id = 1;
$this->robotUser->own_id = 1;
$this->robotUser->department_id = $departments['Bilgi İşlem Müdürlüğü']->id;
$this->robotUser->fillVariables();
$this->robotUser->save();