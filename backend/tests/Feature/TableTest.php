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
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20One&name=test_sql_relation_one_to_one&column_db_type_id=5&column_gui_type_id=6&up_column_id=&column_table_relation_id=33&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
    
    public function testCreateColumnSqlRelationOneToMany()
    {
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20Many&name=test_sql_relation_one_to_many&column_db_type_id=3&column_gui_type_id=7&up_column_id=&column_table_relation_id=33&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createColumnAndTest($url);
    }
}