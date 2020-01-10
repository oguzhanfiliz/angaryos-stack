<?php

$params = str_replace(['&#39;', '&#34;'], ["'", '"'], $params);
return json_decode($params);