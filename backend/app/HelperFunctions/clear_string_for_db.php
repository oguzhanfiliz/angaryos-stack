<?php

if($params == NULL) return NULL;
if($params == '') return '';

if(is_array($params)) 
    return clear_object_for_db($params);
else if(!is_string($params) && !is_numeric($params) && !is_bool($params)) 
    dd('clear_string_for_db.object.type.error:', $params);

return str_replace(["'", '"', '`'], ['&#39;', '&#34;', '&#1034;'], $params);