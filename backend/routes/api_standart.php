<?php

Route::any('/', '\App\Http\Controllers\GeneralController@serviceOk');//Tested
Route::get('/initializeDb', '\App\Http\Controllers\GeneralController@initializeDb');

    
Route::post('login', 'AuthController@login');//Tested

Route::group(['prefix' => '{token}'], function ()
{
    require 'api_binding.php';

    Route::get('test', function()
    {
        $exitCode = Artisan::call('data:entegrator', 
        [
            'tableRelationId' => 6,
        ]);
        
        dd('e: ' . $exitCode);
    });
    
    Route::any('/', '\App\Http\Controllers\GeneralController@serviceOk');
    Route::get('getLoggedInUserInfo', 'AuthController@getLoggedInUserInfo');
    Route::get('getUserToken/{user_id}', 'AuthController@getUserToken');
    
    Route::get('assignAuth', 'AuthController@assignAuth');

    
    
    Route::get('tables/{table_name}/report', 'ReportController@index');
    
    Route::get('tables/{table_name}', 'TableController@index');
    Route::get('tables/{table_name}/deleted', 'TableController@deleted');
    Route::get('tables/{table_name}/create', 'TableController@create');
    Route::any('tables/{table_name}/store', 'TableController@store');    
    Route::get('tables/{table_name}/{id}/edit', 'TableController@edit');
    Route::any('tables/{table_name}/{id}/update', 'TableController@update');
    Route::get('tables/{table_name}/{id}/delete', 'TableController@destroy');
    Route::get('tables/{table_name}/{id}/clone', 'TableController@cloneRecord');
    Route::get('tables/{table_name}/{id}/archive', 'TableController@archive');
    Route::get('tables/{table_name}/{archive_id}/restore', 'TableController@restore');
    Route::get('tables/{table_name}/{id}', 'TableController@show');

    
    Route::get('tables/{table_name}/{id}/getRelationTableData/{tree}', 'TableController@getRelationTableData');
    
    
    Route::get('tables/{table_name}/getSelectColumnData/{column_name}', 'TableController@getSelectColumnData');
    Route::get('tables/{table_name}/{id}/getRelationTableData/{tree}/getSelectColumnData/{column_name}', 'TableController@getSelectColumnDataInRelationTableData');
    Route::get('tables/{table_name}/{id}/archive/getSelectColumnData/{column_name}', 'TableController@getSelectColumnDataInArchive');
    Route::get('tables/{table_name}/deleted/getSelectColumnData/{column_name}', 'TableController@getSelectColumnDataInDeleted');

    
    Route::get('columnGuiTriggers/{table_name}/{column_name}/{triggerName}', 'ColumnGuiTriggerController@index');
    
});