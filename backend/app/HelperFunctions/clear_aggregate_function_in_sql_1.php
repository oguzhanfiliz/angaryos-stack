<?php

if(!strstr($params, 'string_agg(')) return $params;

$temp = explode('string_agg', $params)[1];
$temp = trim($temp, '(');
$temp = trim($temp, ')');

$arr = [];
$control = 0; $i = 0;
$inSingle = FALSE;
$inDouble = FALSE;
for($j = 0; $j < strlen($temp); $j++)
{
    $s = $temp[$j];

    if($s == '(') $control++;
    if($s == ')') $control--;
    if($s == "'") $inSingle = !$inSingle;
    if($s == '"') $inDouble = !$inDouble;

    if($control == 0 && !$inSingle && !$inDouble &&  $s == ',') 
    {
        $i++;
        continue;
    }   
    
    if(!isset($arr[$i])) $arr[$i] = '';

    $arr[$i] .= $s;
}

return $arr[0];