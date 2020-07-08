<?php

//istenilen veri döüş tipinde döndür

global $pipe;
if(isset($pipe['response_injection']))
{
    $params = array_merge($params, $pipe['response_injection']);
} 

return response()->json($params, $params['code']);