<?php

$search = ['1)', "',',", "','", '),', ',', 'string_agg(', 'split_part('];

$params = str_replace($search, '', $params);
$params = trim($params);

return $params;