<?php

Route::options('/{any}', '\App\Http\Controllers\GeneralController@serviceOk');

Route::any('/', '\App\Http\Controllers\GeneralController@serviceOk');
Route::get('/initializeDb', '\App\Http\Controllers\GeneralController@initializeDb');

    
Route::post('login', 'AuthController@login');//Tested

Route::group(['prefix' => '{token}'], function ()
{
    require 'api_binding.php';

    
    Route::any('test', '\App\Http\Controllers\GeneralController@test');
    
    
    Route::get('logs', '\App\Http\Controllers\GeneralController@logs');
    
    Route::any('/', '\App\Http\Controllers\GeneralController@serviceOk');
    Route::get('getLoggedInUserInfo', 'AuthController@getLoggedInUserInfo');
    Route::get('getUserToken/{user_id}', 'AuthController@getUserToken');
    
    Route::get('logOut', 'AuthController@LogOut');
    
    Route::get('assignAuth', 'AuthController@assignAuth');
    
    
    
    Route::get('getMapData', 'MapController@GetData');
    Route::post('translateKmzOrKmlToJson', 'MapController@TranslateKmzOrKmlToJson');
    Route::get('getSubTables/{upTableName}/{type}', 'MapController@GetSubTables');
    
    
    Route::post('importRecord', '\App\Http\Controllers\GeneralController@importRecord');

    
    
    Route::get('tables/{table_name}/report', 'ReportController@index');
    
    Route::get('missions/{mission}', 'MissionController@DoMission');
    
    Route::get('dashboards/getData/{auth}', 'DashboardController@GetData');
    
    Route::get('tables/{table_name}', 'TableController@index');
    Route::get('tables/{table_name}/deleted', 'TableController@deleted');
    Route::get('tables/{table_name}/create', 'TableController@create');
    
    Route::options('tables/{table_name}/store', '\App\Http\Controllers\GeneralController@serviceOk');
    Route::post('tables/{table_name}/store', 'TableController@store');    
    Route::get('tables/{table_name}/store', 'TableController@store');    
    
    Route::get('tables/{table_name}/{id}/edit', 'TableController@edit');
    
    Route::options('tables/{table_name}/{id}/update', '\App\Http\Controllers\GeneralController@serviceOk');
    Route::post('tables/{table_name}/{id}/update', 'TableController@update');
    Route::get('tables/{table_name}/{id}/update', 'TableController@update');
    
    Route::get('tables/{table_name}/{id}/delete', 'TableController@destroy');
    Route::get('tables/{table_name}/{id}/clone', 'TableController@cloneRecord');
    Route::get('tables/{table_name}/{id}/export', 'TableController@export');
    Route::get('tables/{table_name}/{id}/archive', 'TableController@archive');
    Route::get('tables/{table_name}/{archive_id}/restore', 'TableController@restore');
    Route::get('tables/{table_name}/{id}', 'TableController@show');
    Route::get('tables/{table_name}/{id}/getRelationDataInfo/{column_name}', 'TableController@getRelationDataInfo'); 

    
    Route::get('tables/{table_name}/{id}/getRelationTableData/{tree}', 'TableController@getRelationTableData');
    
    
    Route::get('tables/{table_name}/getSelectColumnData/{column_name}', 'TableController@getSelectColumnData');
    Route::get('tables/{table_name}/{id}/getRelationTableData/{tree}/getSelectColumnData/{column_name}', 'TableController@getSelectColumnDataInRelationTableData');
    Route::get('tables/{table_name}/{id}/archive/getSelectColumnData/{column_name}', 'TableController@getSelectColumnDataInArchive');
    Route::get('tables/{table_name}/deleted/getSelectColumnData/{column_name}', 'TableController@getSelectColumnDataInDeleted');

    
    Route::get('columnGuiTriggers/{table_name}/{column_name}/{triggerName}', 'ColumnGuiTriggerController@index');
    
    Route::get('search/{table_name}/{words}', 'TableController@search');

});