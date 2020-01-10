<?php

if(!is_array($params))
    $params = ['message' => $params];

$data = 
[
    'status' => 'error',
    'code' => 400,
    'data' => $params
];

return helper('response', $data);