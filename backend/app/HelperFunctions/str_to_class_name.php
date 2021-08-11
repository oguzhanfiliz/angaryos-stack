<?php

$params = str_replace([' '], '_', $params);
$params = explode('_', $params);

$className = '';
foreach($params as $param)
    $className .= ucfirst(strtolower($param));

return $className;