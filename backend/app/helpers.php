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

function dd_live(...$data)
{
    $userId = @\Auth::user()->id;
    if(strlen($userId) == 0) return;
    
    $ids = json_decode(DEBUG_USER_IDS);
    if(in_array($userId, $ids)) dd($data);
} 