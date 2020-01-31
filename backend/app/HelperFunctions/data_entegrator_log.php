<?php

$userId = \Auth::user()->id;

$log = new App\BaseModel('data_entegrator_logs');
$log->user_id = $userId;
$log->own_id = $userId;
$log->state = TRUE;
$log->name_basic = $params[0];
$log->log = $params[1];

$log->save();