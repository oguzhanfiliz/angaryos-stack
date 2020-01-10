<?php

$tokens = $params->tokens;
$token = Str::random(16).'d'.$params->id;
$temp =
[
    'token' => $token,
    'time' => strtotime(date('Y-m-d H:i:s'))
];
array_push($tokens, $temp);

$params->tokens = $tokens;
$params->save();

return $token;