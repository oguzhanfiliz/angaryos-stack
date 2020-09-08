<?php

$user = \Auth::user();
if($user == NULL) $user = \App\User::find(ROBOT_USER_ID);
$userId = $user->id;

$log =
[
    'user_id' => $userId,
    'level' => $params[0],
    'description' => $params[1],
    'detail' => $params[2]
];

\Log::alert($params[0].' - Data entegrator log: ' . json_encode($log));