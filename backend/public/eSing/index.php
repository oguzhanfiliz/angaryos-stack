<?php

/****    Common Variables    ****/

$dbConnection = NULL;


/****    Helper Functions    ****/

require 'helpers.php';


/****  Main  ****/

$data = require 'control.php';
if($data == NULL) return;

$env = getEnvironments();

$base =
[
    'status' => 'success',
    'code' => 200,
    'data' => []
];

$eSignCount = getESignCount($data[2], $env);
if($eSignCount === FALSE) return;

$base['data']['eSingCount'] = $eSignCount;
$base['data']['waitTime'] = microtime(true)- $pipe['laravelStart'];

bb($base)
?>