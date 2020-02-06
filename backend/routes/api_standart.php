<?php

Route::any('/', '\App\Http\Controllers\GeneralController@serviceOk');//Tested
Route::get('/initializeDb', '\App\Http\Controllers\GeneralController@initializeDb');

    
Route::post('login', 'AuthController@login');//Tested

Route::group(['prefix' => '{token}'], function ()
{
    require 'api_binding.php';

    Route::get('test', function()
    {
        /*$temp = 'ldap://192.168.1.82|cn=admin,dc=liderahenk,dc=org|ssifre';
        $temp = explode('|', $temp);
        
        $ldap = new App\Libraries\LdapHelper($temp[0], $temp[1], $temp[2]);
        
        
        $filter='(ou=*)';
        $entries = $ldap->searchInLdap($filter);
        dd($entries);*/
        //$cron = "*/5 1-2 3 3,4,5 *"; 
        //$result = preg_match( "/^((?:[1-9]?\d|\*)\s*(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?\s*){5}$/", $cron, $matches); 
        //print_r(count($matches) == 2);
dd(4);

        $exitCode = Artisan::call('data:entegrator', 
        [
            'tableRelationId' => 2,
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