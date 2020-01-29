<?php

$return = [];
foreach($params as $file)
{
    $temp = $file->destination_path . $file->file_name;
    array_push($return, $temp);
}

return $return;