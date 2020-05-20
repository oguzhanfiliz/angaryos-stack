<?php

$temp = explode(' from ', $params)[1];
$temp = explode(' as ', $temp)[0];

return $temp;