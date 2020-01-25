<?php

namespace Tests\Feature;

use Tests\TestCase;

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
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnTextType()
    {
        $url = 'tables/columns/store?display_name=Test%20Text&name=test_text&column_db_type_id=2&column_gui_type_id=2&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnJsonType()
    {
        $url = 'tables/columns/store?display_name=Test%20JSON&name=test_json&column_db_type_id=3&column_gui_type_id=4&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnIntType()
    {
        $url = 'tables/columns/store?display_name=Test%20Integer&name=test_integer&column_db_type_id=5&column_gui_type_id=3&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnFloatType()
    {
        $url = 'tables/columns/store?display_name=Test%20Float&name=test_float&column_db_type_id=6&column_gui_type_id=3&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnBooleanType()
    {
        $url = 'tables/columns/store?display_name=Test%20Boolean&name=test_boolean&column_db_type_id=7&column_gui_type_id=8&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnDateType()
    {
        $url = 'tables/columns/store?display_name=Test%20Date&name=test_date&column_db_type_id=8&column_gui_type_id=5&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnTimeType()
    {
        $url = 'tables/columns/store?display_name=Test%20Time&name=test_time&column_db_type_id=9&column_gui_type_id=5&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnDateTimeType()
    {
        $url = 'tables/columns/store?display_name=Test%20DateTime&name=test_datetime&column_db_type_id=10&column_gui_type_id=5&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnPointType()
    {
        $url = 'tables/columns/store?display_name=Test%20Point&name=test_point&column_db_type_id=11&column_gui_type_id=11&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnMultiPointType()
    {
        $url = 'tables/columns/store?display_name=Test%20MultiPoint&name=test_multipoint&column_db_type_id=12&column_gui_type_id=14&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnLinestringType()
    {
        $url = 'tables/columns/store?display_name=Test%20LineString&name=test_linestring&column_db_type_id=13&column_gui_type_id=12&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnMultiLinestringType()
    {
        $url = 'tables/columns/store?display_name=Test%20MultiLineString&name=test_multilinestring&column_db_type_id=14&column_gui_type_id=15&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnPolygonType()
    {
        $url = 'tables/columns/store?display_name=Test%20Polygon&name=test_polygon&column_db_type_id=15&column_gui_type_id=13&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnMultiPolygonType()
    {
        $url = 'tables/columns/store?display_name=Test%20MultiPolygon&name=test_multipolygon&column_db_type_id=16&column_gui_type_id=16&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
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
        $url = 'tables/column_table_relations/store?name_basic=Test%20Types%20Sql%20Relation&relation_table_id=26&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%5D&relation_sql=select%20*%20from%20test_types&relation_source_column=id&relation_display_column=display_name&column_data_source_id=&up_column_id=&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    
    
    public function testCreateColumnSqlRelationOneToOne()
    {
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20One&name=test_sql_relation_one_to_one&column_db_type_id=5&column_gui_type_id=6&up_column_id=&column_table_relation_id=35&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnSqlRelationOneToMany()
    {
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20Many&name=test_sql_relation_one_to_many&column_db_type_id=3&column_gui_type_id=7&up_column_id=&column_table_relation_id=35&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    
    
    public function testCreateJoinTableForRelation()
    {
        $url = 'tables/join_tables/store?name_basic=Users%20department%20relation&join_table_id=1&join_table_alias=users_department&connection_column_with_alias=users.department_id&join_connection_type==&join_column_id=5&description=&state=1&column_set_id=0&in_form_column_name=join_table_ids&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateColumnRelationForJoinTableForRelation()
    {
        $url = 'tables/column_table_relations/store?name_basic=Test%20relation%20table%20column%20default%20relation&relation_table_id=2&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%223%22%5D&relation_sql=&relation_source_column=users.id&relation_display_column=users_department.name_basic&column_data_source_id=&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateJoinedTableColumn()
    {
        $url = 'tables/columns/store?display_name=Test%20relation%20data%20column&name=test_relation_table_column&column_db_type_id=3&column_gui_type_id=7&up_column_id=&column_table_relation_id=36&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    
    
    public function testCreateDataSource()
    {
        $url = 'tables/column_data_sources/store?name=Test%20data%20source&php_code=%3C?php%0A$repository%20=%20new%20App%5CRepositories%5CTestRepository();%0A?%3E&state=1&column_set_id=0&in_form_column_name=column_data_source_id&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateRelationForDataSource()
    {
        $url = 'tables/column_table_relations/store?name_basic=Test%20data%20source%20relation&relation_table_id=&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%5D&relation_sql=&relation_source_column=&relation_display_column=&column_data_source_id=2&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateDataSourceColumn()
    {
        $url = 'tables/columns/store?display_name=Test%20data%20source%20column&name=test_data_source&column_db_type_id=1&column_gui_type_id=6&up_column_id=&column_table_relation_id=37&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    
    
    public function testCreateSubscriber()
    {
        $url = 'tables/subscribers/store?name_basic=Test%20Subscriber&subscriber_type_id=2&php_code=%3C?php%0A%5CDB::table(%27settings%27)-%3Ewhere(%27name%27,%20%27REC_COUNT_PER_PAGE%27)-%3Eupdate(%5B%27value%27%20=%3E%20%2712%27%5D);%0A?%3E&description=&state=1&column_set_id=0&in_form_column_name=subscriber_ids&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateSubscriberColumn()
    {
        $url = 'tables/columns/store?display_name=Test%20Subscriber&name=test_subscriber&column_db_type_id=1&column_gui_type_id=1&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%228%22%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    
    
    public function testCreateValidationRule()
    {
        $url = 'tables/validations/store?display_name=Test%20Validation%20Rule&name=test_validation_rule&php_code=%3C?php%0A$return%20=%20($value%20==%202);%0A?%3E&error_message=&description=&state=1&column_set_id=0&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateColumnValidation()
    {
        $url = 'tables/column_validations/store?validation_with_params=test_validation_rule&description=&state=1&column_set_id=0&in_form_column_name=column_validation_ids&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testValidationColumn()
    {
        $url = 'tables/columns/store?display_name=Test%20Validation&name=test_validation&column_db_type_id=5&column_gui_type_id=3&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%2223%22%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
}