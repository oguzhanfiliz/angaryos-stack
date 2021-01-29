<?php

function helper($function_name, $params = NULL)
{
    $function_name = str_replace('.', '', $function_name);
    return require 'HelperFunctions/'.$function_name.'.php';
}

function read_from_response_data($key, $json = FALSE)
{
    $r = require 'HelperFunctions/'.__FUNCTION__.'.php';

    if($json)
    {
        $r = helper('json_str_to_object', $r);        
        $r = clear_object_for_db($r); 
    }
    
    return $r;
}

function clear_object_for_db($obj)
{
    if($obj == NULL) 
        return $obj;
    else if(is_bool($obj)) 
        return (bool)$obj;
    else if(is_numeric($obj)) 
        return $obj;
    else if(is_string($obj)) 
        return helper('clear_string_for_db', $obj);
    else if(is_array($obj))
    {
        foreach($obj as $key => $val)
        {
            if(is_bool($obj[$key])) 
                $obj[$key] = (bool)$obj[$key];
            else if(is_numeric($obj[$key])) 
                $obj[$key] = $obj[$key];
            else if(is_string($val)) 
                $obj[$key] = helper('clear_string_for_db', $val);
            else
                $obj[$key] = clear_object_for_db($val);
        }  
    }
    else if(is_object($obj))
    {
        if(strstr(strtolower(get_class($obj)), 'file')) return $obj;
        
        foreach(array_keys((array)$obj) as $key)
        {
            try 
            {
                if(is_bool($obj->{$key})) 
                    $obj->{$key} = (bool)$obj->{$key};
                if(is_numeric($obj->{$key}))
                    $obj->{$key} = $obj->{$key};
                else if(is_string($obj->{$key})) 
                    $obj->{$key} = helper('clear_string_for_db', $obj->{$key});
                else
                    $obj->{$key} = clear_object_for_db($obj->{$key});
            } 
            catch (\Exception $e) 
            {
                \Log::alert("helper:".$_SERVER['REQUEST_URI'].':'.json_encode([get_class($obj), array_keys((array)$obj), $obj, $_POST, $e->getMessage()]));
            }
            catch (\Error $e) 
            {
                \Log::alert("helper1:".$_SERVER['REQUEST_URI'].':'.json_encode([get_class($obj), array_keys((array)$obj), $obj, $_POST, $e->getMessage()]));
            }
        }  
    }
    else dd('clear_object_for_db', $obj); 

    return $obj;
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
    $object['response_data'] = \Request::all();
    
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

function dd_live($dump, ...$data)
{
    $userId = @\Auth::user()->id;
    if(strlen($userId) == 0) return;
    
    if(count($data) == 1) $data = $data[0];

    $ids = json_decode(DEBUG_USER_IDS);
    if(in_array($userId, $ids)) 
    { 
        if($dump)
        {
            var_dump($data);
            exit(0);
        }
        else dd($data);
    }
} 

function custom_abort_ext($message)
{
    $userId = @\Auth::user()->id;
    if(strlen($userId) == 0) custom_abort($message);
    
    $ids = json_decode(DEBUG_USER_IDS);
    if(!in_array($userId, $ids)) custom_abort($message);
} 