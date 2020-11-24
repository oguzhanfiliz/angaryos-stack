<?php

if(!isset($_SERVER['REQUEST_URI'])) return;

$temp = explode('?', $_SERVER['REQUEST_URI']);

if(isset($temp[1]))
    if(strstr($temp[1], 'force=true')) 
        return;

$temp = explode('/', trim($temp[0], '/'));
if(count($temp) < 4) return;

if($temp[3] != 'eSignControl') return;

return $temp;

?>