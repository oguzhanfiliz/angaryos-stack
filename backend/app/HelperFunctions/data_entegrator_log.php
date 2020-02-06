<?php

send_log('error', 'Data entegrator log: ' . json_encode($params));

$userId = \Auth::user()->id;

$log = new App\BaseModel('data_entegrator_logs');
$log->user_id = $userId;
$log->own_id = $userId;
$log->state = TRUE;
$log->log_level_id = get_attr_from_cache('log_levels', 'name', $params[0], 'id');
$log->name_basic = $params[1];
$log->log = $params[2];

$log->save();