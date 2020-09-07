<?php

if($params == NULL) return NULL;
if($params == '') return '';

if(is_string($params))
    return filter_var($params, FILTER_SANITIZE_STRING);

foreach($params as $key => $value)
{
    $key = helper('clear_string_for_db', $key);
    $value = helper('clear_string_for_db', $value);

    $params[$key] = $value;
}

return $params;