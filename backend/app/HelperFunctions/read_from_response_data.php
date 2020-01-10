<?php

switch ($method) 
{
    case 'post':
    case 'get':
        return helper('clear_string_for_db', \Request::input($key));
    default:
        abort(helper('response_error', 'undefined.method.for.read_from_response_data'));
}