<?php

function helper($function_name, $params = NULL)
{
    $function_name = str_replace('.', '', $function_name);
    return require 'HelperFunctions/'.$function_name.'.php';
}

function read_from_response_data($method, $key, $json = FALSE)
{
    $r = require 'HelperFunctions/'.__FUNCTION__.'.php';
    if($json)
        $r = helper('json_str_to_object', $r);
    
    return $r;
}

function get_attr_from_cache($tableName, $requestColumn, $requestData, $responseColumn)
{
    return require 'HelperFunctions/'.__FUNCTION__.'.php';
}

function get_model_from_cache($tableName, $requestColumn, $requestData)
{
    return require 'HelperFunctions/'.__FUNCTION__.'.php';
}

function param_is_have($params, $name)
{
    return require 'HelperFunctions/'.__FUNCTION__.'.php';
}

function param_value_is_correct($params, $name, $validation = '*auto*')
{
    return require 'HelperFunctions/'.__FUNCTION__.'.php';
}

function send_log($level, $message, $object = NULL)
{    
    global $pipe;
    
    if($object == NULL) $object = [];
    if(!is_array($object)) $object = (array)$object;
    
    $user = \Auth::user();
    if($user) $object['user'] = $user->id.'-'.$user->name_basic.' '.$user->surname;
    
    $object['waitTime'] = @helper('get_wait_time');    
    $object['logRandom'] = @$pipe['logRandom'];    
    $object['host'] = @$_SERVER['HOSTNAME'];
    $object['uri'] = @$_SERVER['REQUEST_URI'];
    $object['logTime'] = date("Y-m-d h:i:sa");
    
    \App\Jobs\SendLog::dispatch($level, $message, json_encode($object));
}

function custom_abort($response)
{
    return require 'HelperFunctions/'.__FUNCTION__.'.php';
}

function create_new_record($tableName, $data, $user = NULL)
{
    return require 'HelperFunctions/'.__FUNCTION__.'.php';
}

function copy_record_to_archive($record, $tableName = NULL)
{
    return require 'HelperFunctions/'.__FUNCTION__.'.php';
}

function getMemcachedKeys($host = 'memcached', $port = 11211)
{
    $mem = @fsockopen($host, $port);
    if ($mem === FALSE) return -1;

    // retrieve distinct slab
    $r = @fwrite($mem, 'stats items' . chr(10));
    if ($r === FALSE) return -2;

    $slab = array();
    while (($l = @fgets($mem, 1024)) !== FALSE) {
        // sortie ?
        $l = trim($l);
        if ($l == 'END') break;

        $m = array();
        // <STAT items:22:evicted_nonzero 0>
        $r = preg_match('/^STAT\sitems\:(\d+)\:/', $l, $m);
        if ($r != 1) return -3;
        $a_slab = $m[1];

        if (!array_key_exists($a_slab, $slab)) $slab[$a_slab] = array();
    }

    // recuperer les items
    reset($slab);
    foreach ($slab AS $a_slab_key => &$a_slab) {
        $r = @fwrite($mem, 'stats cachedump ' . $a_slab_key . ' 100' . chr(10));
        if ($r === FALSE) return -4;

        while (($l = @fgets($mem, 1024)) !== FALSE) {
            // sortie ?
            $l = trim($l);
            if ($l == 'END') break;

            $m = array();
            // ITEM 42 [118 b; 1354717302 s]
            $r = preg_match('/^ITEM\s([^\s]+)\s/', $l, $m);
            if ($r != 1) return -5;
            $a_key = $m[1];

            $a_slab[] = $a_key;
        }
    }

    // close
    @fclose($mem);
    unset($mem);

    // transform it;
    $keys = array();
    reset($slab);
    foreach ($slab AS &$a_slab) {
        reset($a_slab);
        foreach ($a_slab AS &$a_key) $keys[] = $a_key;
    }
    unset($slab);

    return $keys;
}