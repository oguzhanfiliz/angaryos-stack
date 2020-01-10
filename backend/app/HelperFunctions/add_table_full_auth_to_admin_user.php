<?php

if($params['type'] != 'create') return;

$auths = 
[
    'tables:'.$params['table']['name'].':lists:0',
    'tables:'.$params['table']['name'].':queries:0',
    'tables:'.$params['table']['name'].':shows:0',
    'tables:'.$params['table']['name'].':edits:0',
    'tables:'.$params['table']['name'].':deleteds:0',
    'tables:'.$params['table']['name'].':creates:0',
];

$robotUserId = ROBOT_USER_ID;

$auth = new \App\BaseModel('auth_groups');
$auth->name = $params['table']['display_name'] . ' Tam Yetki';
$auth->auths = $auths;
$auth->state = TRUE;
$auth->own_id = $robotUserId;
$auth->user_id = $robotUserId;
$auth->save();

$adminAuth = get_attr_from_cache('auth_groups', 'id', 1, '*');
$adminAuth->fillVariables();

$temp = $adminAuth->auths;
$temp[count($temp) - 1] = $auth->id;
$adminAuth->auths = $temp;

$adminAuth->save();