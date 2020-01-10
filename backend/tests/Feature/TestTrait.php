<?php

namespace Tests\Feature;

use App\User;

trait TestTrait
{
    /****    Get Data Functions    ****/
    
    private function getToken()
    {
        $user = User::find(1);
        return helper('create_user_token', $user);
    }
    
    private function getBaseUrl()
    {
        return '/api/v1/';
    }
    
    private function getBaseUrlWithToken()
    {
        $base = $this->getBaseUrl();
        $token = $this->getToken();
        return $base.$token.'/';
    }
    
    private function getParamsForTableDataBasic()
    {
        $params[0] = helper('get_null_object');
        $params[0]->column_array_id = 0;
        $params[0]->column_array_id_query = 0;
        $params[0]->limit = 10;
        $params[0]->page = 1;
        $params[0]->sorts = helper('get_null_object');
        $params[0]->filters = helper('get_null_object');
        
        
        
        $params[1] = helper('get_null_object');
        $params[1]->column_array_id = 0;
        $params[1]->column_array_id_query = 0;
        $params[1]->limit = 1;
        $params[1]->page = 2;
        
        $params[1]->sorts = helper('get_null_object');
        $params[1]->sorts->id = FALSE;
        $params[1]->sorts->value = TRUE;
        $params[1]->sorts->user_id = TRUE;
        $params[1]->sorts->updated_at = TRUE;
        
        $params[1]->filters = helper('get_null_object');
        
        $params[1]->filters->id = helper('get_null_object');
        $params[1]->filters->id->type = 2;
        $params[1]->filters->id->guiType = 'numeric';
        $params[1]->filters->id->filter = 2;
        $params[1]->filters->id->check = TRUE;
        
        $params[1]->filters->name = helper('get_null_object');
        $params[1]->filters->name->type = 5;
        $params[1]->filters->name->guiType = 'string';
        $params[1]->filters->name->filter = 'db_schema';
        $params[1]->filters->name->check = TRUE;
        
        $params[1]->filters->description = helper('get_null_object');
        $params[1]->filters->description->type = 100;
        $params[1]->filters->description->guiType = 'text';
        $params[1]->filters->description->filter = '';
        
        $params[1]->filters->state = helper('get_null_object');
        $params[1]->filters->state->type = 1;
        $params[1]->filters->state->guiType = 'boolean';
        $params[1]->filters->state->filter = TRUE;
        $params[1]->filters->state->check = TRUE;
        
        $params[1]->filters->user_id = helper('get_null_object');
        $params[1]->filters->user_id->type = 1;
        $params[1]->filters->user_id->guiType = 'select';
        $params[1]->filters->user_id->filter = ['1'];
                
        return $params;
    }
    
    private function getDataFromResponseOrData($responseOrData)
    {
        if(get_class($responseOrData) == 'Illuminate\Foundation\Testing\TestResponse')
           $responseOrData = $responseOrData->getData()->data;
        
        return $responseOrData;
    }


    
    /****    Common Control Data Functions    ****/
    
    private function controlResponseIsSuccess($response)
    {
        $response->assertStatus(200);
        
        $this->assertEquals(get_class($response), 'Illuminate\Foundation\Testing\TestResponse');
        $this->assertEquals($response->getData()->code, 200);
        $this->assertEquals($response->getData()->status, 'success');
    }
    
    private function controlResponseIsError($response)
    {
        $response->assertStatus(400);
        
        $this->assertEquals(get_class($response), 'Illuminate\Foundation\Testing\TestResponse');
        $this->assertEquals($response->getData()->code, 400);
        $this->assertEquals($response->getData()->status, 'error');
    }
    
    private function controlResponseIsServiceOk($response)
    {
        $this->controlResponseIsSuccess($response);
        $this->assertEquals($response->getData()->data->message, 'service.ok');
    }
    
    private function controlResponseIsFailToken($response)
    {
        $this->controlResponseIsError($response);
        $this->assertEquals($response->getData()->data->message, 'fail.token');
    }
    
    private function controlResponseOrDataHasAttributes($response, $attributes)
    {
        $data = $this->getDataFromResponseOrData($response);
        if(!is_array($attributes)) $attributes = [$attributes];
        
        $keys = array_keys(get_object_vars($data));
        foreach($attributes as $attribute)
            $this->assertTrue(in_array($attribute, $keys));
    }
    
    private function controlResponseHasNumericAttributes($response, $attributes)
    {
        if(!is_array($attributes)) $attributes = [$attributes];
        
        $data = $this->getDataFromResponseOrData($response);
        $this->controlResponseOrDataHasAttributes($data, $attributes);
        
        foreach($attributes as $attribute)
            $this->assertTrue(is_numeric($data->{$attribute}));
    }
    
    private function controlResponseHasBooleanAttributes($response, $attributes)
    {
        if(!is_array($attributes)) $attributes = [$attributes];
        
        $data = $this->getDataFromResponseOrData($response);
        $this->controlResponseOrDataHasAttributes($data, $attributes);
        
        foreach($attributes as $attribute)
            $this->assertTrue(is_bool($data->{$attribute}));
    }
    
    
    
    /****    Control Data Functions    ****/
    
    private function controlTableDataBasic($response) 
    {
        $this->controlResponseIsSuccess($response);
        $this->controlResponseOrDataHasAttributes($response, ['table_info', 'records', 'columns', 'query_columns']);
        $this->controlResponseHasNumericAttributes($response, ['pages', 'all_records_count']);
        
        $data = $this->getDataFromResponseOrData($response);
        
        
        $this->controlResponseOrDataHasAttributes($data->table_info, ['name', 'display_name']);
        
        $this->assertGreaterThan(0, count($data->records));
        
        $columns = array_keys(get_object_vars($data->columns));
        $this->assertGreaterThan(0, count($columns));
        
        $this->controlResponseOrDataHasAttributes($data->records[0], $columns);
        
        $queryColumns = array_keys(get_object_vars($data->query_columns));
        $this->assertGreaterThan(0, count($queryColumns));
    }
    
    private function controlTableDataFull($response) 
    {
        $this->controlTableDataBasic($response);
        
        $data = $response->getData()->data;
        $this->controlResponseHasBooleanAttributes($data->records[0], ['_is_deletable', '_is_exportable', '_is_editable', '_is_restorable', '_is_showable']);
    }
    
    private function controlSelectColumnData($response) 
    {
        $this->controlSelectColumnDataBasic($response);
    }
    
    private function controlSelectColumnDataInRelationTableData($response) 
    {
        $this->controlSelectColumnDataBasic($response);
    }
    
    private function controlSelectColumnDataBasic($response)
    {
        $data = $response->getData();
        $this->controlResponseOrDataHasAttributes($data, ['results', 'pagination']);
    }
    
    private function controlInfoData($response)
    {
        $this->controlResponseOrDataHasAttributes($response, ['table_info', 'record', 'column_set']);
    }
    
    private function controlRelationTableData($response)
    {
        $this->controlTableDataBasic($response);
    }
}
