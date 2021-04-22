<?php

$tableId = get_Attr_from_cache('tables', 'name', $params->getTable(), 'id');

$tokens = $params->tokens;
$token = Str::random(16).'t'.$tableId.'d'.$params->id;

$info = read_from_response_data('clientInfo');
if(is_string($info)) $info = helper('json_str_to_object', $info);

$temp =
[
    'token' => $token,
    'time' => strtotime(date('Y-m-d H:i:s')),
    'clientInfo' => $info
];

if($tokens == NULL) $tokens = [];
array_push($tokens, $temp);

$params->tokens = $tokens;
\DB::table($params->getTable())->where('id', $params->id)->update(['tokens' => $params->tokens]);

//dd($params->getTable(), $params->id, json_decode(DB::table($params->getTable())->find($params->id)->tokens), $tokens);
return $token;