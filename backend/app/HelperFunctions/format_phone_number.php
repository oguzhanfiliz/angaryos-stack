<?php

$paramsOrj = $params;
    
if(substr($params, 0, 1) == '9') $params = substr($params, 1);
if(substr($params, 0, 1) != '0') $params = '0' . $params;
$params = str_replace(' ', '', $params);
if(strlen($params) != 11) return $paramsOrj;


if(preg_match( '/^\\d(\d{3})(\d{3})(\d{2})(\d{2})$/', $params,  $matches))
{
    if(count($matches) != 5) return $paramsOrj;
    unset($matches[0]);
    return '0 ' . implode(' ', $matches);
}

return $paramsOrj;