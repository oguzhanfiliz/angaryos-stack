<?php

global $pipe;

$pipe['asyncPool']->add(function() use($level, $message, $object)
{
    $log = helper('get_null_object');
    $log->message = $message;
    $log->obj = json_encode($object);

    \Log::{$level}(json_encode($log)); 
});