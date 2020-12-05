<?php

return str_replace(['&#39;', '&#34;', '&#1034;'], ["'", '"', '`'], $params);
//return htmlspecialchars_decode($params);