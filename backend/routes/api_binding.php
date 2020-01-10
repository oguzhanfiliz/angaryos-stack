<?php

use \App\BaseModel;

Route::bind('token', function ($token) 
{
    $user = helper('get_user_from_token', $token);
    if($user == NULL) abort(helper('response_error', 'fail.token'));

    Auth::login($user);

    return $user;
});

Route::bind('table_name', function ($tableName) 
{
    $tableId = get_attr_from_cache('tables', 'name', $tableName, 'id');
    if($tableId == NULL) abort(helper('response_error', 'fail.table.name'));

    $model = new BaseModel($tableName);

    return $model;
});

Route::bind('column_name', function ($columnName) 
{
    $column = get_attr_from_cache('columns', 'name', $columnName, '*');
    if($column == NULL) abort(helper('response_error', 'fail.column.name'));
    
    return $column;
});

Route::bind('id', function ($id) 
{    
    if(!is_numeric($id)) abort(helper('response_error', 'fail.id'));
    
    global $pipe;    
    $model = get_attr_from_cache($pipe['table'], 'id', $id, '*');    
    if($model == NULL) abort(helper('response_error', 'fail.id'));
    
    return $model;
});

Route::bind('archive_id', function ($id) 
{
    
    if(!is_numeric($id)) abort(helper('response_error', 'fail.archive_id'));
    
    global $pipe;
    $model = get_attr_from_cache($pipe['table'].'_archive', 'id', $id, '*');    
    if($model == NULL) abort(helper('response_error', 'fail.archive_id'));
    
    return $model;
});