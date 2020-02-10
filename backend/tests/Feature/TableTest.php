<?php

namespace Tests\Feature;

use Tests\TestCase;

use DB;

class TableTest extends TestCase
{
    use TestTrait;
    
    
    
    
    
    /****    Create Table: Test Types    ****/
    
    public function testGetTablesForm()
    {
        $url = 'tables/tables/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url);
    }
    
    public function testSearchInSelectColumnColumnIds()
    {
        $url = 'tables/tables/getSelectColumnData/column_ids?search=ad&page=1&limit=500&upColumnName=table_id';
        $response = $this->standartTest($url, FALSE);
        $this->controlResponseOrDataHasAttributes($response->getData(), ['results', 'pagination']);
    }
    
    
    
    public function testCreateTableTestType()
    {
        $url = 'tables/tables/store?display_name=Test%20Types&name=test_types&column_ids=%5B%2226%22,%227%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    
    
    
    
    /****    Create Columns (All Types)    ****/
    
    public function testGetColumnsForm()
    {
        $url = 'tables/columns/create?params=%7B%22column_set_id%22:%222%22%7D&';
        $this->standartTest($url);
    }
    
    public function testSearchInSelectColumnColumnDbTypeId()
    {
        $url = 'tables/columns/getSelectColumnData/column_db_type_id?search=k%C4%B1sa';
        $response = $this->standartTest($url, FALSE);
        $this->controlResponseOrDataHasAttributes($response->getData(), ['results', 'pagination']);
    }
    
    public function testSearchInSelectColumnColumnGuiTypeId()
    {
        $url = 'tables/columns/getSelectColumnData/column_gui_type_id?search=k%C4%B1sa';
        $response = $this->standartTest($url, FALSE);
        $this->controlResponseOrDataHasAttributes($response->getData(), ['results', 'pagination']);
    }
    
    
    
    public function testCreateColumnStringType()
    {
        $url = 'tables/columns/store?display_name=Test%20String&name=test_string&column_db_type_id=1&column_gui_type_id=1&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnTextType()
    {
        $url = 'tables/columns/store?display_name=Test%20Text&name=test_text&column_db_type_id=2&column_gui_type_id=2&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnJsonType()
    {
        $url = 'tables/columns/store?display_name=Test%20JSON&name=test_json&column_db_type_id=3&column_gui_type_id=4&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnIntType()
    {
        $url = 'tables/columns/store?display_name=Test%20Integer&name=test_integer&column_db_type_id=5&column_gui_type_id=3&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnFloatType()
    {
        $url = 'tables/columns/store?display_name=Test%20Float&name=test_float&column_db_type_id=6&column_gui_type_id=3&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnBooleanType()
    {
        $url = 'tables/columns/store?display_name=Test%20Boolean&name=test_boolean&column_db_type_id=7&column_gui_type_id=8&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnDateType()
    {
        $url = 'tables/columns/store?display_name=Test%20Date&name=test_date&column_db_type_id=8&column_gui_type_id=5&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnTimeType()
    {
        $url = 'tables/columns/store?display_name=Test%20Time&name=test_time&column_db_type_id=9&column_gui_type_id=5&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnDateTimeType()
    {
        $url = 'tables/columns/store?display_name=Test%20DateTime&name=test_datetime&column_db_type_id=10&column_gui_type_id=5&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnPointType()
    {
        $url = 'tables/columns/store?display_name=Test%20Point&name=test_point&column_db_type_id=11&column_gui_type_id=11&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnMultiPointType()
    {
        $url = 'tables/columns/store?display_name=Test%20MultiPoint&name=test_multipoint&column_db_type_id=12&column_gui_type_id=14&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnLinestringType()
    {
        $url = 'tables/columns/store?display_name=Test%20LineString&name=test_linestring&column_db_type_id=13&column_gui_type_id=12&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnMultiLinestringType()
    {
        $url = 'tables/columns/store?display_name=Test%20MultiLineString&name=test_multilinestring&column_db_type_id=14&column_gui_type_id=15&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnPolygonType()
    {
        $url = 'tables/columns/store?display_name=Test%20Polygon&name=test_polygon&column_db_type_id=15&column_gui_type_id=13&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnMultiPolygonType()
    {
        $url = 'tables/columns/store?display_name=Test%20MultiPolygon&name=test_multipolygon&column_db_type_id=16&column_gui_type_id=16&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testColumnCanNotBeSameName()
    {
        $url = 'tables/columns/store?display_name=Test%20String&name=test_string&column_db_type_id=1&column_gui_type_id=1&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'error');
    }
    
    
    
    public function testGetColumnTableRelationForm()
    {
        $url = 'tables/column_table_relations/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url);
    }
    
    public function testColumnTableRelationTestSqlRelation()
    {
        $url = 'tables/column_table_relations/store?name_basic=Test%20Types%20Sql%20Relation&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%5D&relation_sql=select%20*%20from%20test_types&relation_source_column=id&relation_display_column=display_name&column_data_source_id=&up_column_id=&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateColumnSqlRelationOneToOne()
    {
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20One&name=test_sql_relation_one_to_one&column_db_type_id=5&column_gui_type_id=6&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnSqlRelationOneToMany()
    {
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20Many&name=test_sql_relation_one_to_many&column_db_type_id=3&column_gui_type_id=7&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateJoinTableForRelation()
    {
        $url = 'tables/join_tables/store?name_basic=Users%20department%20relation&join_table_id=1&join_table_alias=users_department&connection_column_with_alias=users.department_id&join_connection_type==&join_column_id=5&description=&state=1&column_set_id=0&in_form_column_name=join_table_ids&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnRelationForJoinTableForRelation()
    {
        $id = $this->getLastId('join_tables');
        
        $url = 'tables/column_table_relations/store?name_basic=Test%20relation%20table%20column%20default%20relation&relation_table_id=2&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%22'.$id.'%22%5D&relation_sql=&relation_source_column=users.id&relation_display_column=users_department.name_basic&column_data_source_id=&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id';
        $this->createAndTest($url);
    }
    
    public function testCreateJoinedTableColumn()
    {
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Relation%20Data%20Column&name=test_relation_table_column&column_db_type_id=3&column_gui_type_id=7&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateDataSource()
    {
        $url = 'tables/column_data_sources/store?name=Test%20data%20source&php_code=%3C?php%0A$repository%20=%20new%20App%5CRepositories%5CTestRepository();%0A?%3E&state=1&column_set_id=0&in_form_column_name=column_data_source_id&';
        $this->createAndTest($url);
    }
    
    public function testCreateRelationForDataSource()
    {
        $id = $this->getLastId('column_data_sources');
        
        $url = 'tables/column_table_relations/store?name_basic=Test%20data%20source%20relation&relation_table_id=&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%5D&relation_sql=&relation_source_column=&relation_display_column=&column_data_source_id='.$id.'&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id&';
        $this->createAndTest($url);
    }
    
    public function testCreateDataSourceColumn()
    {
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Data%20Source%20Column&name=test_data_source&column_db_type_id=1&column_gui_type_id=6&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testUpColumnRule()
    {
        $columnId = get_attr_from_cache('columns', 'name', 'test_integer', 'id');
        
        $url = 'tables/up_columns/store?name_basic=Test%20i%C3%A7in%20%C3%BCst%20kolon&column_id='.$columnId.'&table_ids=%5B%5D&php_code=%3C?php%20%0A$return%20=%20%5B(int)$data%5D;%0A?%3E&description=&state=1&column_set_id=0&in_form_column_name=up_column_id&';
        $this->createAndTest($url);
    }
    
    public function testUpColumn()
    {
        $upColumnId = $this->getLastId('up_columns');
        $realtionId = get_attr_from_cache('columns', 'name', 'test_types_id', 'column_table_relation_id');

        $url = 'tables/columns/store?display_name=Test%20Up%20Column&name=test_up_column&column_db_type_id=5&column_gui_type_id=6&up_column_id='.$upColumnId.'&column_table_relation_id='.$realtionId.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateSubscriber()
    {
        $url = 'tables/subscribers/store?name_basic=Test%20Subscriber&subscriber_type_id=2&php_code=%3C?php%0A%5CDB::table(%27settings%27)-%3Ewhere(%27name%27,%20%27REC_COUNT_PER_PAGE%27)-%3Eupdate(%5B%27value%27%20=%3E%20%2712%27%5D);%0A?%3E&description=&state=1&column_set_id=0&in_form_column_name=subscriber_ids&';
        $this->createAndTest($url);
    }
    
    public function testCreateSubscriberColumn()
    {
        $id = $this->getLastId('subscribers');
        
        $url = 'tables/columns/store?display_name=Test%20Subscriber&name=test_subscriber&column_db_type_id=1&column_gui_type_id=1&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%22'.$id.'%22%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateValidationRule()
    {
        $url = 'tables/validations/store?display_name=Test%20Validation%20Rule&name=test_validation_rule&php_code=%3C?php%0A$return%20=%20($value%20==%202);%0A?%3E&error_message=&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnValidation()
    {
        $url = 'tables/column_validations/store?validation_with_params=test_validation_rule&description=&state=1&column_set_id=0&in_form_column_name=column_validation_ids&';
        $this->createAndTest($url);
    }
    
    public function testValidationColumn()
    {
        $id = $this->getLastId('column_validations');
        
        $url = 'tables/columns/store?display_name=Test%20Validation&name=test_validation&column_db_type_id=5&column_gui_type_id=3&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%22'.$id.'%22%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCollectiveInfoColumn()
    {
        $url = 'tables/columns/store?display_name=Test%20Collective%20Info&name=test_collective_info&column_db_type_id=1&column_gui_type_id=1&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=5&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCraeteTestTable()
    {
        $temp = DB::table('columns')->select('id')->whereRaw('name ilike \'%test%\'')->get();
        
        $columnIds = [];
        foreach($temp as $colId) array_push ($columnIds, $colId->id);
        
        $url = 'tables/tables/store?display_name=Test&name=test&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
       
        $response = $this->createAndTest($url);
    }
    
    public function testTableEditForm()
    {
        $tableId = $this->getLastId('tables');
        $url = 'tables/tables/'.$tableId.'/edit?params=%7B%22column_set_id%22:%220%22%7D&';
        
        $response = $this->standartTest($url); 
    }
    
    public function testTableEdit1()
    {
        $columnIds = [];
        $columns = helper('get_all_columns_from_db', 'test');
        foreach($columns as $columnName => $temp)
        {
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        $id = get_attr_from_cache('columns', 'name', 'description', 'id');
        array_push ($columnIds, $id);
        
        $tableId = $this->getLastId('tables');
        
        $url = 'tables/tables/'.$tableId.'/update?display_name=Test22&name=test22&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    /*public function testTableEdit2()
    {
        $columnIds = [];
        
        $columns = helper('get_all_columns_from_db', 'test2');
        unset($columns['description']);
        
        foreach($columns as $columnName => $temp)
        {
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        
        $tableId = $this->getLastId('tables');
               
        $url = 'tables/tables/'.$tableId.'/update?display_name=Test&name=test&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }*/
}