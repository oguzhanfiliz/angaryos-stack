<?php

$params = helper('reverse_clear_string_for_db', $params);
$params = str_replace(['<?php', '<?', '?>'], '', $params);

return $params;