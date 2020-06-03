<?php

$logLevelName = $params[0];
$logLevelId = get_attr_from_cache('log_levels', 'name', $params[0], 'id');

send_log($logLevelName, 'Data entegrator log: ' . json_encode($params));

$user = \Auth::user();
if($user == NULL) $user = \App\User::find(ROBOT_USER_ID);
$userId = $user->id;

$log = new App\BaseModel('data_entegrator_logs');
$log->user_id = $userId;
$log->own_id = $userId;
$log->state = TRUE;
$log->log_level_id = $logLevelId;
$log->name_basic = $params[1];
$log->log = $params[2];

$log->save();