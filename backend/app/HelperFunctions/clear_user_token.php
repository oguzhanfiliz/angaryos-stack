<?php

$tokens = [];
foreach($params->tokens as $i => $token)
{
    if(!is_array($token)) continue;
    
    $temp = date('Y-m-d H:i:s', $token['time']);
    $token_time = new \Carbon\Carbon($temp);
    $interval = $token_time->diffInDays(\Carbon\Carbon::now());
    if($interval > 5) continue;
    
    array_push($tokens, $token);
}

$params->tokens = $tokens;

\DB::table('users')->where('id', $params->id)->update(['tokens' => $params->tokens]);

return $params;