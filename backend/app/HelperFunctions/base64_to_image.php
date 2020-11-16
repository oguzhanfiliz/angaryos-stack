<?php

if (!preg_match('/^data:image\/(\w+);base64,/', $params)) return;

$ext = '';
$params = explode(',', $params);
if(strstr($params[0], 'image/jpeg')) $ext = 'jpeg';
else if(strstr($params[0], 'image/jpg')) $ext = 'jpg';

if($ext == '') return;

$data = base64_decode($params[1]);

return ['ext' => $ext, 'image' => $data];