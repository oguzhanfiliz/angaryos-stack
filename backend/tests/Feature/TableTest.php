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
        global $pipe;
        $pipe['testing'] = TRUE;
        
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
        $nameId = get_attr_from_cache('columns', 'name', 'name', 'id');
        
        $url = 'tables/tables/store?display_name=Test%20Types&name=test_types&column_ids=%5B%22'.$nameId.'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
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
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'string', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'string', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20String&name=test_string&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnTextType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'text', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'text', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Text&name=test_text&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnJsonType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'jsonb', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'jsonb', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20JSON&name=test_json&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnIntType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'integer', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'numeric', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Integer&name=test_integer&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnFloatType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'float', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'numeric', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Float&name=test_float&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnBooleanType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'boolean', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'boolean', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Boolean&name=test_boolean&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnDateType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'date', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'date', 'id');
        $validationId = get_attr_from_cache('column_validations', 'validation_with_params', 'date_format:Y-m-d', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Date&name=test_date&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B'.$validationId.'%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnTimeType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'time', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'time', 'id');
        $validationId = get_attr_from_cache('column_validations', 'validation_with_params', 'date_format:H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Time&name=test_time&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B'.$validationId.'%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnDateTimeType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'datetime', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'datetime', 'id');
        $validationId = get_attr_from_cache('column_validations', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20DateTime&name=test_datetime&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B'.$validationId.'%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnPointType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'point', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'point', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Point&name=test_point&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnMultiPointType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'multipoint', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'multipoint', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20MultiPoint&name=test_multipoint&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnLinestringType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'linestring', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'linestring', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20LineString&name=test_linestring&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnMultiLinestringType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'multilinestring', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'multilinestring', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20MultiLineString&name=test_multilinestring&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnPolygonType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'polygon', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'polygon', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Polygon&name=test_polygon&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnMultiPolygonType()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'multipolygon', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'multipolygon', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20MultiPolygon&name=test_multipolygon&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&srid=7932&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testColumnCanNotBeSameName()
    {
        $url = 'tables/columns/store?display_name=Test%20String&name=test_string&column_db_type_id=1&column_gui_type_id=1&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'error');
    }
    
    
    
    
    
    /****    Create Releated Columns    ****/
        
    public function testGetColumnTableRelationForm()
    {
        $url = 'tables/column_table_relations/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url);
    }
    
    public function testColumnTableRelationTestSqlRelation()
    {
        $url = 'tables/column_table_relations/store?name_basic=Test%20Types%20Sql%20Relation&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%5D&relation_sql=select%20*%20from%20test_types&relation_source_column=id&relation_display_column=name&column_data_source_id=&up_column_id=&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateColumnSqlRelationOneToOne()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'integer', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'select', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20One&name=test_sql_relation_one_to_one&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnSqlRelationOneToMany()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'jsonb', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'multiselect', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Sql%20Relation%20One%20To%20Many&name=test_sql_relation_one_to_many&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateUsersJoinTableForRelation()
    {
        $url = 'tables/join_tables/store?name_basic=users%20relation%20join&join_table_id=2&join_table_alias=users&connection_column_with_alias=test.test_relation_table_column&join_column_id=5&description=&state=1&column_set_id=0&in_form_column_name=join_table_ids&';
        $this->createAndTest($url);
    }
    
    public function testCreateDepartmentsJoinTableForRelation()
    {
        $url = 'tables/join_tables/store?name_basic=Users%20department%20relation&join_table_id=1&join_table_alias=users_department&connection_column_with_alias=users.department_id&join_connection_type==&join_column_id=5&description=&state=1&column_set_id=0&in_form_column_name=join_table_ids&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnRelationForJoinTableForRelation()
    {
        $id = $this->getLastId('join_tables');
        
        $url = 'tables/column_table_relations/store?name_basic=Test%20relation%20table%20column%20default%20relation&relation_table_id=2&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%22'.($id-1).'%22, %22'.$id.'%22%5D&relation_sql=&relation_source_column=users.id&relation_display_column=users_department.name_basic&column_data_source_id=&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id';
        $this->createAndTest($url);
    }
    
    public function testCreateJoinedTableColumn()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'integer', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'select', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Relation%20Data%20Column&name=test_relation_table_column&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
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
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'integer', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'select', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $id = $this->getLastId('column_table_relations');
        
        $url = 'tables/columns/store?display_name=Test%20Data%20Source%20Column&name=test_data_source&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id='.$id.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
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
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'integer', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'select', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $upColumnId = $this->getLastId('up_columns');
        $realtionId = get_attr_from_cache('columns', 'name', 'test_types_id', 'column_table_relation_id');

        $url = 'tables/columns/store?display_name=Test%20Up%20Column&name=test_up_column&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id='.$upColumnId.'&column_table_relation_id='.$realtionId.'&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    
    
    /****    Other Column Testing    ****/    
    
    public function testCreateSubscriber()
    {
        $url = 'tables/subscribers/store?name_basic=Test%20Subscriber&subscriber_type_id=2&php_code=%3C?php%0A%5CDB::table(%27settings%27)-%3Ewhere(%27name%27,%20%27REC_COUNT_PER_PAGE%27)-%3Eupdate(%5B%27value%27%20=%3E%20%2712%27%5D);%0A?%3E&description=&state=1&column_set_id=0&in_form_column_name=subscriber_ids&';
        $this->createAndTest($url);
    }
    
    public function testCreateSubscriberColumn()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'string', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'string', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $id = $this->getLastId('subscribers');
        
        $url = 'tables/columns/store?display_name=Test%20Subscriber&name=test_subscriber&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%22'.$id.'%22%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCreateValidationRule()
    {
        $url = 'tables/validations/store?display_name=Test%20Validation%20Rule&name=test_validation_rule&php_code=%3C?php%0A$return%20=%20($value%20==%202);%0A?%3E&error_message=De%C4%9Fer%20yaln%C4%B1zca%202%20olabilir&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    public function testCreateColumnValidation()
    {
        $url = 'tables/column_validations/store?validation_with_params=test_validation_rule&description=&state=1&column_set_id=0&in_form_column_name=column_validation_ids&';
        $this->createAndTest($url);
    }
    
    public function testValidationColumn()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'string', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'string', 'id');
        //$validationId = get_attr_from_cache('column_validation', 'validation_with_params', 'date_format:Y-m-d H:i:s', 'id');
        
        $id = $this->getLastId('column_validations');
        
        $url = 'tables/columns/store?display_name=Test%20Validation&name=test_validation&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%22'.$id.'%22%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    public function testCollectiveInfoColumn()
    {
        $dbTypeId = get_attr_from_cache('column_db_types', 'name', 'string', 'id');
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'string', 'id');
        $id = get_attr_from_cache('column_collective_infos', 'name', 'count', 'id');
        
        $url = 'tables/columns/store?display_name=Test%20Collective%20Info&name=test_collective_info&column_db_type_id='.$dbTypeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id='.$id.'&default=&description=&state=1&column_set_id=2&';
        $this->createAndTest($url);
    }
    
    
    
    
    
    /****    Create And Update Test Table    ****/
    
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
        
        $this->standartTest($url); 
    }
    
    public function testTableEditAddDescriptionColumn()
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
    
    public function testTableEditRemoveDescriptionColumnInColumnIds()
    {
        $columnIds = [];
        
        $columns = helper('get_all_columns_from_db', 'test22');
        unset($columns['description']);
        
        foreach($columns as $columnName => $temp)
        {
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        
        $tableId = $this->getLastId('tables');
               
        $url = 'tables/tables/'.$tableId.'/update?display_name=Test&name=test&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    public function testTableEditAddNameBasicColumn()
    {
        $columnIds = [];
        $columns = helper('get_all_columns_from_db', 'test');
        foreach($columns as $columnName => $temp)
        {
            if(substr($columnName, 0, 8) == 'deleted_') continue;
            
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        $id = get_attr_from_cache('columns', 'name', 'name_basic', 'id');
        array_push ($columnIds, $id);
        
        $tableId = $this->getLastId('tables');
        
        $url = 'tables/tables/'.$tableId.'/update?display_name=Test&name=test&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    
    
    
    /****    Column Array and Set Testings    ****/
    
    public function testColumnSetForm()
    {
        $url = 'tables/column_sets/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url); 
    }
    
    public function testColumnArrayForm()
    {
        $url = 'tables/column_arrays/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url); 
    }
    
    
    public function testColumnArrayDirectData()
    {
        $columnIds = [];
        $columns = ['test_types_id','test_types_ids','test_string','test_text','test_integer','test_boolean'];
        foreach($columns as $columnName)
        {
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        
        $url = 'tables/column_arrays/store?name_basic=Test%20Kolon%20Dizisi&column_array_type_id=1&table_id='.$tableId.'&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&join_table_ids=%5B%5D&description=&state=1&column_set_id=0&in_form_column_name=column_array_ids&';
        $this->createAndTest($url);
    }
    
    
    public function testColumnArrayJoinForm()
    {
        $url = 'tables/join_tables/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url); 
    }
    
    public function testColumnArrayJoinCreate()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'users', 'id');
        $columnId = get_attr_from_cache('columns', 'name', 'id', 'id');
        
        $url = 'tables/join_tables/store?name_basic=Test%20join%20kolon&join_table_id='.$tableId.'&join_table_alias=kaydin_sahibi&connection_column_with_alias=test.own_id&join_column_id='.$columnId.'&description=&state=1&column_set_id=0&in_form_column_name=join_table_ids&';
        $this->createAndTest($url);
    }
    
    public function testColumnArrayDirectDataWithJoin()
    {
        $columnIds = [];
        $columns = ['test_json','test_date','name_basic'];
        foreach($columns as $columnName)
        {
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        $joinId = $this->getLastId('join_tables');
        
        $url = 'tables/column_arrays/store?name_basic=Test%20join%20&column_array_type_id=1&table_id='.$tableId.'&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&join_table_ids=%5B%22'.$joinId.'%22%5D&description=&state=1&column_set_id=0&in_form_column_name=column_array_ids&join_columns=kaydin_sahibi.name_basic';
        $this->createAndTest($url);
    }
    
    
    public function testColumnSetCreate()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        $arrayId = $this->getLastId('column_arrays');
        
        $url = 'tables/column_sets/store?name_basic=Test%20kolon%20seti&table_id='.$tableId.'&column_set_type_id=1&column_array_ids=%5B%22'.($arrayId-1).'%22,%22'.$arrayId.'%22%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    
    
    
    public function testJoinForColumnArrayJoinedDataTableForm()
    {
        $url = 'tables/join_tables/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url); 
    }
    
    public function testJoinForColumnArrayJoinedDataTableCreate()
    {
        $joinTableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        $joinColumnId = get_attr_from_cache('columns', 'name', 'test_types_id', 'id');
        
        $url = 'tables/join_tables/store?name_basic=Bilgi%20kart%C4%B1nda%20g%C3%B6sterilecek%20olan%20bu%20tipteki%20kay%C4%B1tlar%20tablosu%20i%C3%A7in%20tablo%20ili%C5%9Fkisi&join_table_id='.$joinTableId.'&join_table_alias=bu_tiptekiler&connection_column_with_alias=test_types.id&join_column_id='.$joinColumnId.'&description=&state=1&column_set_id=0&in_form_column_name=join_table_ids&';
        $this->createAndTest($url);
    }
    
    
    public function testColumnArrayJoinedDataTable()
    {
        $joinId = $this->getLastId('join_tables');
        $tableId = get_attr_from_cache('tables', 'name', 'test_types', 'id');
        
        $url = 'tables/column_arrays/store?name_basic=Test%20tipleri%20bilgi%20kart%C4%B1%20i%C3%A7in%20bunu%20i%C3%A7erenler%20ba%C4%9Fl%C4%B1%20data&column_array_type_id=2&table_id='.$tableId.'&column_ids=%5B%5D&join_table_ids=%5B%22'.$joinId.'%22%5D&join_columns=bu_tiptekiler.id,%20bu_tiptekiler.test_text&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    public function testColumnSetCreateForTestTypesInfoCard()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'test_types', 'id');
        $arrayId = $this->getLastId('column_arrays');
        
        $url = 'tables/column_sets/store?name_basic=Test%20tipler%20bilgi%20karti&table_id='.$tableId.'&column_set_type_id=1&column_array_ids=%5B%22'.$arrayId.'%22%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    
    
    
    
    /**** Other Test Table Testing    ****/
    
    public function testDeleteColumnInTable()
    {
        $columnIds = [];
        
        $columns = helper('get_all_columns_from_db', 'test');
        unset($columns['name_basic']);
        
        foreach($columns as $columnName => $temp)
        {
            if(substr($columnName, 0, 8) == 'deleted_') continue;
            
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
               
        $url = 'tables/tables/'.$tableId.'/update?display_name=Test&name=test&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
        
        $columnId = get_attr_from_cache('columns', 'name', 'name_basic', 'id');
        $arrayId = $this->getLastId('column_arrays') - 1;
        $columnIds = get_attr_from_cache('column_arrays', 'id', $arrayId, 'column_ids');
        $columnIds = json_decode($columnIds);
        
        $this->assertTrue(!in_array($columnId, $columnIds));
    }
    
    public function testColumnForm()
    {
        $id = get_attr_from_cache('columns', 'name', 'test_string', 'id');
        $url = 'tables/columns/'.$id.'/edit?params=%7B%22column_set_id%22:%222%22%7D&';
        $this->standartTest($url); 
    }
    
    public function testUpdateColumnNameAndDBType()
    {
        $guiTypeId = get_attr_from_cache('column_gui_types', 'name', 'string', 'id');
        
        $id = get_attr_from_cache('columns', 'name', 'test_string', 'id');
        $typeId = get_attr_from_cache('column_db_types', 'name', 'text', 'id');
        
        $url = 'tables/columns/'.$id.'/update?display_name=Test%20String&name=test_string2&column_db_type_id='.$typeId.'&column_gui_type_id='.$guiTypeId.'&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&state=1&column_set_id=2&';
        $this->standartTest($url); 
        
        $dbTypeId = get_attr_from_cache('columns', 'name', 'test_string2', 'column_db_type_id');
        $dbTypeName = get_attr_from_cache('column_db_types', 'id', $dbTypeId, 'name');
        
        $this->assertEquals($dbTypeName, 'text');
    }
    
    public function testDeleteColumn()
    {
        $id = get_attr_from_cache('columns', 'name', 'test_string2', 'id');
        $url = 'tables/columns/'.$id.'/delete?';
        $this->standartTest($url); 
    }
    
    
    
    public function testColumnArrayArchiveHadDeletedColumn()
    {
        $arrayId = $this->getLastId('column_arrays') - 1;
        
        $url = 'tables/column_arrays/'.$arrayId.'/archive?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22name_basic%22,%22column_array_type_id%22,%22table_id%22,%22column_ids%22,%22join_table_ids%22,%22join_columns%22,%22description%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22record_id%22%5D%7D&';
        $this->standartTest($url);
    }
    
    public function testRestoreColumnArrayHasDeletedColumn()
    {
        $arrayId = $this->getLastId('column_arrays') - 2;
        $archive = DB::table('column_arrays_archive')->where('record_id', $arrayId)->first();
        
        $url = 'tables/column_arrays/'.$archive->id.'/restore?';
        $resposne = $this->standartTest($url, FALSE); 
        
        $this->controlResponseIsError($resposne);
    }
    
    
    public function testTableArchiveHadDeletedColumn()
    {
        $id = get_attr_from_cache('tables', 'name', 'test', 'id');
        
        $url = 'tables/tables/'.$id.'/archive?params=%7B%22page%22:1,%22limit%22:%2210%22,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%5D%7D&';
        $this->standartTest($url);
    }
    
    public function testRestoreTableHasDeletedColumn()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        
        $id = get_attr_from_cache('columns', 'name', 'deleted_test_string2', 'id');
        $archive = DB::table('tables_archive')->where('record_id', $tableId)->whereRaw('column_ids @> \''.$id.'\'::jsonb')->first();
        
        $url = 'tables/tables/'.$archive->id.'/restore?';
        $resposne = $this->standartTest($url, FALSE); 
        
        $this->controlResponseIsError($resposne);
        
    }
    
    public function testShowDeletedTablesAndColumnsSettingOn()
    {
        global $pipe;
        
        $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] = 1;
        
        
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        
        $id = get_attr_from_cache('columns', 'name', 'deleted_test_string2', 'id');
        $archive = DB::table('tables_archive')->where('record_id', $tableId)->whereRaw('column_ids @> \''.$id.'\'::jsonb')->first();
        
        $url = 'tables/tables/'.$archive->id.'/restore?';        
        $this->standartTest($url);
        
        
        $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] = 0;
    }
    
    
    public function testRenameDeletedColumnName()
    {
        $id = get_attr_from_cache('columns', 'name', 'deleted_test_string2', 'id');
        
        $url = 'tables/columns/'.$id.'/update?name=test_string2&column_set_id=2&in_form_column_name=name&single_column=name&';
        $this->standartTest($url);
    }
    
    public function testTableEditDeleteColumnInColumnIds()
    {
        $columnIds = [];
        
        $columns = helper('get_all_columns_from_db', 'test');
        unset($columns['test_string2']);
        
        foreach($columns as $columnName => $temp)
        {
            if(substr($columnName, 0, 8) == 'deleted_') continue;
            
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        $url = 'tables/tables/'.$tableId.'/update?display_name=Test&name=test&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
        $columns = array_keys(helper('get_all_columns_from_db', 'test'));
        
        $this->assertTrue(in_array('deleted_test_string2', $columns));
    }
    
    public function testTableEditAddColumnInColumnIds()
    {
        $columnIds = [];
        
        $columns = helper('get_all_columns_from_db', 'test');
        $columns['test_string2'] = TRUE;
        
        foreach($columns as $columnName => $temp)
        {
            if(substr($columnName, 0, 8) == 'deleted_') continue;
            
            $id = get_attr_from_cache('columns', 'name', $columnName, 'id');
            array_push ($columnIds, $id);
        }
        
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        $url = 'tables/tables/'.$tableId.'/update?display_name=Test&name=test&column_ids=%5B%22'.implode('%22,%22', $columnIds).'%22%5D&subscriber_ids=%5B%5D&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
        $columns = array_keys(helper('get_all_columns_from_db', 'test'));
        $this->assertTrue(in_array('deleted_test_string2', $columns));
        $this->assertTrue(in_array('test_string2', $columns));
    }
    
    
    
    
    
    /****    Table Data Testing    ****/
    
    public function testCreateRecordInTestTypesTable()
    {
        $url = 'tables/test_types/store?name=11&state=1&column_set_id=0&in_form_column_name=test_sql_relation_one_to_many&';
        $this->standartTest($url); 
        
        $url = 'tables/test_types/store?name=22&state=1&column_set_id=0&in_form_column_name=test_sql_relation_one_to_many&';
        $this->standartTest($url); 
        
        $url = 'tables/test_types/store?name=33&state=1&column_set_id=0&in_form_column_name=test_sql_relation_one_to_many&';
        $this->standartTest($url); 
    }
    
    
    
    public function testRecordListForTestTable()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 0);
    }
    
    
    
    public function testGetTestTablesForm()
    {
        $url = 'tables/test/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url);
    }
    
    public function testCreateRecordInTestTable()
    {
        $val = DB::table('settings')->where('name', 'REC_COUNT_PER_PAGE')->first()->value;
        $this->assertEquals($val, 10);
        
        $url = 'tables/test/store?test_types_id=2&test_types_ids=%5B%223%22,%221%22%5D&test_text=text&test_json=%7B%22key%22:%22value%22%7D&test_integer=2&test_float=2.5&test_boolean=1&test_date=2020-10-10&test_time=10:10:10&test_datetime=2020-10-10%2010:10:10&test_sql_relation_one_to_one=2&test_sql_relation_one_to_many=%5B%221%22,%223%22%5D&test_relation_table_column=3&test_data_source=1&test_up_column=2&test_subscriber=takip&test_validation=2&test_collective_info=say%C4%B1&state=1&test_point=POINT(500296.3504744077%204360042.947008081)&test_multipoint=MULTIPOINT((501674.9885891296%204365025.164237745),(542155.4166571172%204401803.223615149),(543694.9824701024%204294156.0081978375))&test_linestring=LINESTRING(539791.7106241542%204403666.148956954,499309.8627388567%204365496.13876998,545620.6651558914%204291313.926532225)&test_multilinestring=MULTILINESTRING((536510.3161746992%204400836.3763518585,497890.72026143764%204365025.2699939655,547561.9169682949%204286092.32242632),(412503.78673065436%204546667.643690126,436116.9847208359%204499808.445167866,495613.29936116654%204515259.171593788))&test_polygon=POLYGON((483707.61619883927%204368810.287091862,484163.1469173294%204360328.295979012,516830.412430472%204356558.077680764,516332.09838221973%204368339.4419512255,483707.61619883927%204368810.287091862))&test_multipolygon=MULTIPOLYGON(((495257.0538544743%204312007.254340158,496199.0874504032%204290158.574827923,608982.9169399985%204285193.175228426,593342.2467403072%204314454.4276410835,495257.0538544743%204312007.254340158),(470762.1083034324%204383829.749333064,464072.77716352727%204365976.680736833,529365.5548243275%204360293.927066676,530231.3571629297%204381012.957183905,470762.1083034324%204383829.749333064),(520686.2784951155%204419441.095496964,518846.6137785723%204403512.883159852,576279.845450729%204402931.008698384,575080.4639442589%204429134.602346605,520686.2784951155%204419441.095496964)))&test_string2=string&column_set_id=0&';
        $this->createAndTest($url);
        
        $val = DB::table('settings')->where('name', 'REC_COUNT_PER_PAGE')->first()->value;
        $this->assertEquals($val, 12);
    }
    
    
    
    public function testRecordListForTestTableCount1()
    {
       $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 1);
    }
    
    
    
    public function testRecordListForTestTableSearchCount1()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:10,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%22id%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%221%22%7D,%22test_types_id%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_types_ids%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_text%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22text%22%7D,%22test_integer%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222%22%7D,%22test_float%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222.5%22%7D,%22test_datetime%22:%7B%22type%22:1,%22guiType%22:%22datetime%22,%22filter%22:%222020-10-10%2010:10:10%22%7D,%22test_time%22:%7B%22type%22:1,%22guiType%22:%22time%22,%22filter%22:%2210:10:10%22%7D,%22test_date%22:%7B%22type%22:1,%22guiType%22:%22date%22,%22filter%22:%222020-10-10%22%7D,%22test_sql_relation_one_to_one%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_sql_relation_one_to_many%22:%7B%22type%22:2,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_relation_table_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%223%22%5D%7D,%22test_json%22:%7B%22type%22:1,%22guiType%22:%22jsonb%22,%22filter%22:%22value%22%7D,%22test_data_source%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22%5D%7D,%22test_up_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_subscriber%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22aki%22%7D,%22test_validation%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%222%22%7D,%22test_collective_info%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22say%22%7D,%22state%22:%7B%22type%22:1,%22guiType%22:%22boolean%22,%22filter%22:true%7D,%22test_point%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.840923192763963%2039.54199010206801,29.555278661513963%2039.2958617269567,30.060649755263963%2039.08724896056893,30.401225927138963%2039.31286399195005,30.12656772401396%2039.57097484638416,29.840923192763963%2039.54199010206801))%5C%22%5D%22%7D,%22test_multipoint%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.37376010682646%2040.00218731463838,29.439922216201463%2039.44449145999397,30.088115575576467%2038.394629943559096,31.538310888076463%2038.77678320670171,31.164775731826467%2039.982591594864715,30.37376010682646%2040.00218731463838))%5C%22%5D%22%7D,%22test_linestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.714580419326463%2039.905337656872604,29.17625034120146%2038.929314926634504,31.082378270888967%2038.29836994619325,31.384502294326467%2039.896909467002644,29.714580419326463%2039.905337656872604))%5C%22%5D%22%7D,%22test_multilinestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((28.659892919326474%2042.02871507390867,26.57249057557647%2040.43422143299068,30.08811557557647%2037.76618191430528,32.90061557557647%2039.499616189260934,31.593242528701467%2041.1412515216102,28.659892919326474%2042.02871507390867))%5C%22%5D%22%7D,%22test_polygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.428935888076463%2039.94325168147171,29.417949559951463%2039.00192300154083,31.263652684951467%2038.91649401273952,30.945049169326463%2039.9474630547941,29.428935888076463%2039.94325168147171))%5C%22%5D%22%7D,%22test_multipolygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.066142919326463%2040.36729011589824,28.59397495057646%2039.27035058594265,30.67588413026396%2038.09547008146154,31.42844760682646%2038.59092539524093,31.400981786513963%2039.69009513242494,30.066142919326463%2040.36729011589824))%5C%22%5D%22%7D,%22test_string2%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22st%22%7D%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 1);
    }
    
    public function testRecordListForTestTableSearchCount0()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:10,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%22id%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%221%22%7D,%22test_types_id%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_types_ids%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_text%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22text%22%7D,%22test_integer%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222%22%7D,%22test_float%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222.5%22%7D,%22test_datetime%22:%7B%22type%22:1,%22guiType%22:%22datetime%22,%22filter%22:%222020-10-10%2010:10:10%22%7D,%22test_time%22:%7B%22type%22:1,%22guiType%22:%22time%22,%22filter%22:%2210:10:10%22%7D,%22test_date%22:%7B%22type%22:1,%22guiType%22:%22date%22,%22filter%22:%222020-10-10%22%7D,%22test_sql_relation_one_to_one%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_sql_relation_one_to_many%22:%7B%22type%22:2,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_relation_table_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%223%22%5D%7D,%22test_json%22:%7B%22type%22:1,%22guiType%22:%22jsonb%22,%22filter%22:%22value%22%7D,%22test_data_source%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22%5D%7D,%22test_up_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_subscriber%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22aki%22%7D,%22test_validation%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%222%22%7D,%22test_collective_info%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22say%22%7D,%22state%22:%7B%22type%22:1,%22guiType%22:%22boolean%22,%22filter%22:true%7D,%22test_point%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.840923192763963%2039.54199010206801,29.555278661513963%2039.2958617269567,30.060649755263963%2039.08724896056893,30.401225927138963%2039.31286399195005,30.12656772401396%2039.23319666772409,29.764018895888967%2039.33948814362469,29.840923192763963%2039.54199010206801))%5C%22%5D%22%7D,%22test_multipoint%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.37376010682646%2040.00218731463838,29.439922216201463%2039.44449145999397,30.088115575576467%2038.394629943559096,31.538310888076463%2038.77678320670171,31.164775731826467%2039.982591594864715,30.37376010682646%2040.00218731463838))%5C%22%5D%22%7D,%22test_linestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.714580419326463%2039.905337656872604,29.17625034120146%2038.929314926634504,31.082378270888967%2038.29836994619325,31.384502294326467%2039.896909467002644,29.714580419326463%2039.905337656872604))%5C%22%5D%22%7D,%22test_multilinestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((28.659892919326474%2042.02871507390867,26.57249057557647%2040.43422143299068,30.08811557557647%2037.76618191430528,32.90061557557647%2039.499616189260934,31.593242528701467%2041.1412515216102,28.659892919326474%2042.02871507390867))%5C%22%5D%22%7D,%22test_polygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.428935888076463%2039.94325168147171,29.417949559951463%2039.00192300154083,31.263652684951467%2038.91649401273952,30.945049169326463%2039.9474630547941,29.428935888076463%2039.94325168147171))%5C%22%5D%22%7D,%22test_multipolygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.066142919326463%2040.36729011589824,28.59397495057646%2039.27035058594265,30.67588413026396%2038.09547008146154,31.42844760682646%2038.59092539524093,31.400981786513963%2039.69009513242494,30.066142919326463%2040.36729011589824))%5C%22%5D%22%7D,%22test_string2%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22st%22%7D%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 0);
    }
    
    
    
    public function testGetTestTablesEditForm()
    {
        $url = 'tables/test/1/edit?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url);
    }
    
    public function testEditRecordInTestTable()
    {
        $url = 'tables/test/1/update?test_types_id=1&test_types_ids=%5B%222%22%5D&test_text=text2&test_json=%7B%22key%22:%20%22value2%22%7D&test_integer=1&test_float=1.55&test_boolean=0&test_date=2021-11-11&test_time=11:11:11&test_datetime=2021-11-11%2011:11:11&test_sql_relation_one_to_one=1&test_sql_relation_one_to_many=%5B%222%22%5D&test_relation_table_column=1&test_data_source=1&test_up_column=1&test_subscriber=takip2&test_validation=2&test_collective_info=say%C4%B12&state=0&test_point=POINT(542413.3481516596%204404979.974009083)&test_multipoint=MULTIPOINT((538659.0012141236%204394908.871874277),(542155.4166571172%204401803.223615147),(549478.9153646851%204397786.509620963))&test_linestring=LINESTRING(539791.7106241543%204403666.148956949,545461.1441879733%204400107.308891944,555844.8720481701%204396891.647917922)&test_multilinestring=MULTILINESTRING((536510.3161746992%204400836.3763518585,525675.4541125065%204402360.543738427,526657.5894516747%204390632.6574061485,542203.4277570777%204393049.828107768,555370.3881850499%204397357.461451976),(543582.854487955%204399158.272595523,544913.3136270003%204413226.325854135,551554.5417693495%204404366.125459144))&test_polygon=POLYGON((526326.7852792103%204418288.727691905,524995.2360197576%204394383.005465006,573493.4703110072%204398916.932342357,561142.9423846025%204411933.866331217,526326.7852792103%204418288.727691905))&test_multipolygon=MULTIPOLYGON(((560674.6753226822%204381823.782481589,545037.3473572036%204392053.508589284,583169.9832812712%204397065.258778284,590779.8062670751%204391047.8451107815,560674.6753226822%204381823.782481589),(504025.3989107517%204393331.330528443,504959.2235168679%204406933.452269536,539351.7979458389%204397654.702565053,530231.3571629298%204381012.9571838975,504025.3989107517%204393331.330528443),(520686.2784951161%204419441.09549696,518846.6137785717%204403512.883159847,576279.8454507357%204402931.008698374,575080.4639442655%204429134.602346594,520686.2784951161%204419441.09549696)))&test_string2=string2&column_set_id=0&';
        $this->createAndTest($url);
    }
    
        
    
    public function testRecordListForTestTableSearchCount0A()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:10,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%22id%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%221%22%7D,%22test_types_id%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_types_ids%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_text%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22text%22%7D,%22test_integer%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222%22%7D,%22test_float%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222.5%22%7D,%22test_datetime%22:%7B%22type%22:1,%22guiType%22:%22datetime%22,%22filter%22:%222020-10-10%2010:10:10%22%7D,%22test_time%22:%7B%22type%22:1,%22guiType%22:%22time%22,%22filter%22:%2210:10:10%22%7D,%22test_date%22:%7B%22type%22:1,%22guiType%22:%22date%22,%22filter%22:%222020-10-10%22%7D,%22test_sql_relation_one_to_one%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_sql_relation_one_to_many%22:%7B%22type%22:2,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_relation_table_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%223%22%5D%7D,%22test_json%22:%7B%22type%22:1,%22guiType%22:%22jsonb%22,%22filter%22:%22value%22%7D,%22test_data_source%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22%5D%7D,%22test_up_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_subscriber%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22aki%22%7D,%22test_validation%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%222%22%7D,%22test_collective_info%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22say%22%7D,%22state%22:%7B%22type%22:1,%22guiType%22:%22boolean%22,%22filter%22:true%7D,%22test_point%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.840923192763963%2039.54199010206801,29.555278661513963%2039.2958617269567,30.060649755263963%2039.08724896056893,30.401225927138963%2039.31286399195005,30.12656772401396%2039.23319666772409,29.764018895888967%2039.33948814362469,29.840923192763963%2039.54199010206801))%5C%22%5D%22%7D,%22test_multipoint%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.37376010682646%2040.00218731463838,29.439922216201463%2039.44449145999397,30.088115575576467%2038.394629943559096,31.538310888076463%2038.77678320670171,31.164775731826467%2039.982591594864715,30.37376010682646%2040.00218731463838))%5C%22%5D%22%7D,%22test_linestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.714580419326463%2039.905337656872604,29.17625034120146%2038.929314926634504,31.082378270888967%2038.29836994619325,31.384502294326467%2039.896909467002644,29.714580419326463%2039.905337656872604))%5C%22%5D%22%7D,%22test_multilinestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((28.659892919326474%2042.02871507390867,26.57249057557647%2040.43422143299068,30.08811557557647%2037.76618191430528,32.90061557557647%2039.499616189260934,31.593242528701467%2041.1412515216102,28.659892919326474%2042.02871507390867))%5C%22%5D%22%7D,%22test_polygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.428935888076463%2039.94325168147171,29.417949559951463%2039.00192300154083,31.263652684951467%2038.91649401273952,30.945049169326463%2039.9474630547941,29.428935888076463%2039.94325168147171))%5C%22%5D%22%7D,%22test_multipolygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.066142919326463%2040.36729011589824,28.59397495057646%2039.27035058594265,30.67588413026396%2038.09547008146154,31.42844760682646%2038.59092539524093,31.400981786513963%2039.69009513242494,30.066142919326463%2040.36729011589824))%5C%22%5D%22%7D,%22test_string2%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22st%22%7D%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 0);
    }
    
    
    
    public function testArchiveListForTestTable()
    {
        $url = 'tables/test/1/archive?params=%7B%22page%22:1,%22limit%22:%2210%22,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%5D%7D&';
        $this->standartTest($url);
    }
    
    public function testRestoreRecordForTestTable()
    {
        $url = 'tables/test/1/restore?';
        $this->createAndTest($url);
    }
    
    public function testRecordListForTestTableSearchCount1A()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:10,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%22id%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%221%22%7D,%22test_types_id%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_types_ids%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_text%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22text%22%7D,%22test_integer%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222%22%7D,%22test_float%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222.5%22%7D,%22test_datetime%22:%7B%22type%22:1,%22guiType%22:%22datetime%22,%22filter%22:%222020-10-10%2010:10:10%22%7D,%22test_time%22:%7B%22type%22:1,%22guiType%22:%22time%22,%22filter%22:%2210:10:10%22%7D,%22test_date%22:%7B%22type%22:1,%22guiType%22:%22date%22,%22filter%22:%222020-10-10%22%7D,%22test_sql_relation_one_to_one%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_sql_relation_one_to_many%22:%7B%22type%22:2,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_relation_table_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%223%22%5D%7D,%22test_json%22:%7B%22type%22:1,%22guiType%22:%22jsonb%22,%22filter%22:%22value%22%7D,%22test_data_source%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22%5D%7D,%22test_up_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_subscriber%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22aki%22%7D,%22test_validation%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%222%22%7D,%22test_collective_info%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22say%22%7D,%22state%22:%7B%22type%22:1,%22guiType%22:%22boolean%22,%22filter%22:true%7D,%22test_point%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.840923192763963%2039.54199010206801,29.555278661513963%2039.2958617269567,30.060649755263963%2039.08724896056893,30.401225927138963%2039.31286399195005,30.12656772401396%2039.57097484638416,29.840923192763963%2039.54199010206801))%5C%22%5D%22%7D,%22test_multipoint%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.37376010682646%2040.00218731463838,29.439922216201463%2039.44449145999397,30.088115575576467%2038.394629943559096,31.538310888076463%2038.77678320670171,31.164775731826467%2039.982591594864715,30.37376010682646%2040.00218731463838))%5C%22%5D%22%7D,%22test_linestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.714580419326463%2039.905337656872604,29.17625034120146%2038.929314926634504,31.082378270888967%2038.29836994619325,31.384502294326467%2039.896909467002644,29.714580419326463%2039.905337656872604))%5C%22%5D%22%7D,%22test_multilinestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((28.659892919326474%2042.02871507390867,26.57249057557647%2040.43422143299068,30.08811557557647%2037.76618191430528,32.90061557557647%2039.499616189260934,31.593242528701467%2041.1412515216102,28.659892919326474%2042.02871507390867))%5C%22%5D%22%7D,%22test_polygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.428935888076463%2039.94325168147171,29.417949559951463%2039.00192300154083,31.263652684951467%2038.91649401273952,30.945049169326463%2039.9474630547941,29.428935888076463%2039.94325168147171))%5C%22%5D%22%7D,%22test_multipolygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.066142919326463%2040.36729011589824,28.59397495057646%2039.27035058594265,30.67588413026396%2038.09547008146154,31.42844760682646%2038.59092539524093,31.400981786513963%2039.69009513242494,30.066142919326463%2040.36729011589824))%5C%22%5D%22%7D,%22test_string2%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22st%22%7D%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 1);
    }
    
    
    
    public function testRecordInfoCardInTestTable()
    {
        $url = 'tables/test/1?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url);
    }
    
    
    
    public function testDeleteRecordForTestTable()
    {
        $url = 'tables/test/1/delete?';
        $this->createAndTest($url);
    }
    
    public function testRecordListForTestTableSearchCount0B()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:10,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%22id%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%221%22%7D,%22test_types_id%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_types_ids%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_text%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22text%22%7D,%22test_integer%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222%22%7D,%22test_float%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222.5%22%7D,%22test_datetime%22:%7B%22type%22:1,%22guiType%22:%22datetime%22,%22filter%22:%222020-10-10%2010:10:10%22%7D,%22test_time%22:%7B%22type%22:1,%22guiType%22:%22time%22,%22filter%22:%2210:10:10%22%7D,%22test_date%22:%7B%22type%22:1,%22guiType%22:%22date%22,%22filter%22:%222020-10-10%22%7D,%22test_sql_relation_one_to_one%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_sql_relation_one_to_many%22:%7B%22type%22:2,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_relation_table_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%223%22%5D%7D,%22test_json%22:%7B%22type%22:1,%22guiType%22:%22jsonb%22,%22filter%22:%22value%22%7D,%22test_data_source%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22%5D%7D,%22test_up_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_subscriber%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22aki%22%7D,%22test_validation%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%222%22%7D,%22test_collective_info%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22say%22%7D,%22state%22:%7B%22type%22:1,%22guiType%22:%22boolean%22,%22filter%22:true%7D,%22test_point%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.840923192763963%2039.54199010206801,29.555278661513963%2039.2958617269567,30.060649755263963%2039.08724896056893,30.401225927138963%2039.31286399195005,30.12656772401396%2039.23319666772409,29.764018895888967%2039.33948814362469,29.840923192763963%2039.54199010206801))%5C%22%5D%22%7D,%22test_multipoint%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.37376010682646%2040.00218731463838,29.439922216201463%2039.44449145999397,30.088115575576467%2038.394629943559096,31.538310888076463%2038.77678320670171,31.164775731826467%2039.982591594864715,30.37376010682646%2040.00218731463838))%5C%22%5D%22%7D,%22test_linestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.714580419326463%2039.905337656872604,29.17625034120146%2038.929314926634504,31.082378270888967%2038.29836994619325,31.384502294326467%2039.896909467002644,29.714580419326463%2039.905337656872604))%5C%22%5D%22%7D,%22test_multilinestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((28.659892919326474%2042.02871507390867,26.57249057557647%2040.43422143299068,30.08811557557647%2037.76618191430528,32.90061557557647%2039.499616189260934,31.593242528701467%2041.1412515216102,28.659892919326474%2042.02871507390867))%5C%22%5D%22%7D,%22test_polygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.428935888076463%2039.94325168147171,29.417949559951463%2039.00192300154083,31.263652684951467%2038.91649401273952,30.945049169326463%2039.9474630547941,29.428935888076463%2039.94325168147171))%5C%22%5D%22%7D,%22test_multipolygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.066142919326463%2040.36729011589824,28.59397495057646%2039.27035058594265,30.67588413026396%2038.09547008146154,31.42844760682646%2038.59092539524093,31.400981786513963%2039.69009513242494,30.066142919326463%2040.36729011589824))%5C%22%5D%22%7D,%22test_string2%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22st%22%7D%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 0);
    }
    
    public function testDeletedListForTestTable()
    {
        $url = 'tables/test/deleted?params=%7B%22page%22:1,%22limit%22:%2210%22,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%5D%7D&';
        $this->standartTest($url);
    }
    
    
    
    public function testRestoreDeletedRecordForTestTable()
    {
        $url = 'tables/test/1/restore?';
        $this->createAndTest($url);
    }
    
    public function testRecordListForTestTableSearchCount1B()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:10,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%22id%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%221%22%7D,%22test_types_id%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_types_ids%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_text%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22text%22%7D,%22test_integer%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222%22%7D,%22test_float%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222.5%22%7D,%22test_datetime%22:%7B%22type%22:1,%22guiType%22:%22datetime%22,%22filter%22:%222020-10-10%2010:10:10%22%7D,%22test_time%22:%7B%22type%22:1,%22guiType%22:%22time%22,%22filter%22:%2210:10:10%22%7D,%22test_date%22:%7B%22type%22:1,%22guiType%22:%22date%22,%22filter%22:%222020-10-10%22%7D,%22test_sql_relation_one_to_one%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_sql_relation_one_to_many%22:%7B%22type%22:2,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_relation_table_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%223%22%5D%7D,%22test_json%22:%7B%22type%22:1,%22guiType%22:%22jsonb%22,%22filter%22:%22value%22%7D,%22test_data_source%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22%5D%7D,%22test_up_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_subscriber%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22aki%22%7D,%22test_validation%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%222%22%7D,%22test_collective_info%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22say%22%7D,%22state%22:%7B%22type%22:1,%22guiType%22:%22boolean%22,%22filter%22:true%7D,%22test_point%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.840923192763963%2039.54199010206801,29.555278661513963%2039.2958617269567,30.060649755263963%2039.08724896056893,30.401225927138963%2039.31286399195005,30.12656772401396%2039.57097484638416,29.840923192763963%2039.54199010206801))%5C%22%5D%22%7D,%22test_multipoint%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.37376010682646%2040.00218731463838,29.439922216201463%2039.44449145999397,30.088115575576467%2038.394629943559096,31.538310888076463%2038.77678320670171,31.164775731826467%2039.982591594864715,30.37376010682646%2040.00218731463838))%5C%22%5D%22%7D,%22test_linestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.714580419326463%2039.905337656872604,29.17625034120146%2038.929314926634504,31.082378270888967%2038.29836994619325,31.384502294326467%2039.896909467002644,29.714580419326463%2039.905337656872604))%5C%22%5D%22%7D,%22test_multilinestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((28.659892919326474%2042.02871507390867,26.57249057557647%2040.43422143299068,30.08811557557647%2037.76618191430528,32.90061557557647%2039.499616189260934,31.593242528701467%2041.1412515216102,28.659892919326474%2042.02871507390867))%5C%22%5D%22%7D,%22test_polygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.428935888076463%2039.94325168147171,29.417949559951463%2039.00192300154083,31.263652684951467%2038.91649401273952,30.945049169326463%2039.9474630547941,29.428935888076463%2039.94325168147171))%5C%22%5D%22%7D,%22test_multipolygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.066142919326463%2040.36729011589824,28.59397495057646%2039.27035058594265,30.67588413026396%2038.09547008146154,31.42844760682646%2038.59092539524093,31.400981786513963%2039.69009513242494,30.066142919326463%2040.36729011589824))%5C%22%5D%22%7D,%22test_string2%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22st%22%7D%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 1);
    }
    
    
    
    
    
    /****    Table Auth Testing    ****/
    
    public function testNoQueryWithAuthColumn()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%22id%22:%7B%22type%22:4,%22guiType%22:%22numeric%22,%22filter%22:%220%22,%22columnName%22:%22id%22%7D,%22auths%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_types_ids%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_text%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22text%22%7D,%22test_integer%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222%22%7D,%22test_float%22:%7B%22type%22:1,%22guiType%22:%22numeric%22,%22filter%22:%222.5%22%7D,%22test_datetime%22:%7B%22type%22:1,%22guiType%22:%22datetime%22,%22filter%22:%222020-10-10%2010:10:10%22%7D,%22test_time%22:%7B%22type%22:1,%22guiType%22:%22time%22,%22filter%22:%2210:10:10%22%7D,%22test_date%22:%7B%22type%22:1,%22guiType%22:%22date%22,%22filter%22:%222020-10-10%22%7D,%22test_sql_relation_one_to_one%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_sql_relation_one_to_many%22:%7B%22type%22:2,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22,%223%22%5D%7D,%22test_relation_table_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%223%22%5D%7D,%22test_json%22:%7B%22type%22:1,%22guiType%22:%22jsonb%22,%22filter%22:%22value%22%7D,%22test_data_source%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%221%22%5D%7D,%22test_up_column%22:%7B%22type%22:1,%22guiType%22:%22multiselect%22,%22filter%22:%5B%222%22%5D%7D,%22test_subscriber%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22aki%22%7D,%22test_validation%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%222%22%7D,%22test_collective_info%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22say%22%7D,%22state%22:%7B%22type%22:1,%22guiType%22:%22boolean%22,%22filter%22:true%7D,%22test_point%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.840923192763963%2039.54199010206801,29.555278661513963%2039.2958617269567,30.060649755263963%2039.08724896056893,30.401225927138963%2039.31286399195005,30.247417333388963%2039.49471503828903,30.022197606826456%2039.62599926684226,29.840923192763963%2039.54199010206801))%5C%22%5D%22%7D,%22test_multipoint%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.37376010682646%2040.00218731463838,29.439922216201463%2039.44449145999397,30.088115575576467%2038.394629943559096,31.538310888076463%2038.77678320670171,31.164775731826467%2039.982591594864715,30.37376010682646%2040.00218731463838))%5C%22%5D%22%7D,%22test_linestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.714580419326463%2039.905337656872604,29.17625034120146%2038.929314926634504,31.082378270888967%2038.29836994619325,31.384502294326467%2039.896909467002644,29.714580419326463%2039.905337656872604))%5C%22%5D%22%7D,%22test_multilinestring%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((28.659892919326474%2042.02871507390867,26.57249057557647%2040.43422143299068,30.08811557557647%2037.76618191430528,32.90061557557647%2039.499616189260934,31.593242528701467%2041.1412515216102,28.659892919326474%2042.02871507390867))%5C%22%5D%22%7D,%22test_polygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((29.428935888076463%2039.94325168147171,29.417949559951463%2039.00192300154083,31.263652684951467%2038.91649401273952,30.945049169326463%2039.9474630547941,29.428935888076463%2039.94325168147171))%5C%22%5D%22%7D,%22test_multipolygon%22:%7B%22type%22:1,%22guiType%22:%22multipolygon%22,%22filter%22:%22%5B%5C%22POLYGON((30.066142919326463%2040.36729011589824,28.59397495057646%2039.27035058594265,30.67588413026396%2038.09547008146154,31.42844760682646%2038.59092539524093,31.400981786513963%2039.69009513242494,30.066142919326463%2040.36729011589824))%5C%22%5D%22%7D,%22test_string2%22:%7B%22type%22:1,%22guiType%22:%22string%22,%22filter%22:%22st%22%7D%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth.column.auths.for.query');
    }
    
    
    
    public function testDeleteDeletedListAuth()
    {
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        
        $temp = [];
        foreach($auths as $auth)
            if($auth != 'tables:test:deleteds:0')
                array_push($temp, $auth);
            
        $temp = DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
        
        $this->assertEquals(TRUE, $temp);
    }
    
    public function testGetDeletedList()
    {
        $url = 'tables/test/deleted?params=%7B%22page%22:1,%22limit%22:10,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22record_id%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    
    
    public function testRestoreFilterForTestTable()
    {
        //Create filter
        $url = 'tables/data_filters/store?name_basic=Filter%20test%20for%20restore&data_filter_type_id=4&sql_code=test.id%20%3E%205&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
                
        
        //Add Filter In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $temp = json_decode($authGroup->auths);
        array_push($temp, 'filters:test:restore:'.$id);
        DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
    }
    
    public function testRestoreForTestTable1()
    {
        $url = 'tables/test/1/restore?';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testArchiveListForTestTable1()
    {
        $url = 'tables/test/1/archive?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22name_basic%22,%22column_array_type_id%22,%22table_id%22,%22column_ids%22,%22join_table_ids%22,%22join_columns%22,%22description%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22record_id%22%5D%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testDeleteRestoreAuthAndFilter()
    {
        //Remove Filter and List Auth In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        
        $temp = [];
        foreach($auths as $auth)
            if($auth != 'filters:test:restore:'.$id && $auth != 'tables:test:restore:0')
                array_push($temp, $auth);
            
        $temp = DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
        
        $this->assertEquals(TRUE, $temp);
    }
    
    public function testRestoreForTestTable2()
    {
        $url = 'tables/test/1/restore?';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testArchiveListForTestTable2()
    {
        $url = 'tables/test/1/archive?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22name_basic%22,%22column_array_type_id%22,%22table_id%22,%22column_ids%22,%22join_table_ids%22,%22join_columns%22,%22description%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22record_id%22%5D%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    
    
    
    public function testDeleteFilterForTestTable()
    {
        //Create filter
        $url = 'tables/data_filters/store?name_basic=Filter%20test%20for%20delete&data_filter_type_id=3&sql_code=test.id%20%3E%205&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
                
        
        //Add Filter In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $temp = json_decode($authGroup->auths);
        array_push($temp, 'filters:test:delete:'.$id);
        DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
    }
    
    public function testDeleteRecordForTestTable1()
    {
        $url = 'tables/test/1/delete?';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testDeleteRecordDeleteAuthAndFilter()
    {
        //Remove Filter and List Auth In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        
        $temp = [];
        foreach($auths as $auth)
            if($auth != 'filters:test:delete:'.$id && $auth != 'tables:test:delete:0')
                array_push($temp, $auth);
            
        $temp = DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
        
        $this->assertEquals(TRUE, $temp);
    }
    
    public function testDeleteRecordForTestTable2()
    {
        $url = 'tables/test/1/delete?';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    
    
    
    public function testUpdateFilterForTestTable()
    {
        //Create filter
        $url = 'tables/data_filters/store?name_basic=Filter%20test%20for%20update&data_filter_type_id=2&sql_code=test.id%20%3E%205&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
                
        
        //Add Filter In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $temp = json_decode($authGroup->auths);
        array_push($temp, 'filters:test:update:'.$id);
        DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
    }
    
    public function testUpdateFormForTestTable1()
    {
        $url = 'tables/test/1/edit?params=%7B%22column_set_id%22:%220%22%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testUpdateRecordForTestTable1()
    {
        $url = 'tables/test/1/update?test_types_id=2&test_types_ids=%5B%223%22,%221%22%5D&test_text=text&test_json=%7B%22key%22:%20%22value%22%7D&test_integer=2&test_float=2.5&test_boolean=1&test_date=2020-10-10&test_time=10:10:10&test_datetime=2020-10-10%2010:10:10&test_sql_relation_one_to_one=2&test_sql_relation_one_to_many=%5B%221%22,%223%22%5D&test_relation_table_column=3&test_data_source=1&test_up_column=2&test_subscriber=takip&test_validation=2&test_collective_info=say%C4%B1&state=1&test_point=POINT(500296.350474408%204360042.94700808)&test_multipoint=MULTIPOINT(501674.98858913%204365025.16423775,542155.416657117%204401803.22361515,543694.982470102%204294156.00819784)&test_linestring=LINESTRING(539791.710624154%204403666.14895695,499309.862738857%204365496.13876998,545620.665155891%204291313.92653222)&test_multilinestring=MULTILINESTRING((536510.316174699%204400836.37635186,497890.720261438%204365025.26999397,547561.916968295%204286092.32242632),(412503.786730654%204546667.64369013,436116.984720836%204499808.44516787,495613.299361167%204515259.17159379))&test_polygon=POLYGON((483707.616198839%204368810.28709186,484163.146917329%204360328.29597901,516830.412430472%204356558.07768076,516332.09838222%204368339.44195123,483707.616198839%204368810.28709186))&test_multipolygon=MULTIPOLYGON(((495257.053854474%204312007.25434016,496199.087450403%204290158.57482792,608982.916939999%204285193.17522843,593342.246740307%204314454.42764108,495257.053854474%204312007.25434016),(470762.108303432%204383829.74933306,464072.777163527%204365976.68073683,529365.554824327%204360293.92706668,530231.35716293%204381012.9571839,470762.108303432%204383829.74933306),(520686.278495116%204419441.09549696,518846.613778572%204403512.88315985,576279.845450729%204402931.00869838,575080.463944259%204429134.6023466,520686.278495116%204419441.09549696)))&test_string2=string&column_set_id=0&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testDeleteRecordUpdateAuthAndFilter()
    {
        //Remove Filter and List Auth In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        
        $temp = [];
        foreach($auths as $auth)
            if($auth != 'filters:test:update:'.$id && $auth != 'tables:test:edits:0')
                array_push($temp, $auth);
            
        $temp = DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
        
        $this->assertEquals(TRUE, $temp);
    }
    
    public function testUpdateFormForTestTable2()
    {
        $url = 'tables/test/1/edit?params=%7B%22column_set_id%22:%220%22%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testUpdateRecordForTestTable2()
    {
        $url = 'tables/test/1/update?test_types_id=2&test_types_ids=%5B%223%22,%221%22%5D&test_text=text&test_json=%7B%22key%22:%20%22value%22%7D&test_integer=2&test_float=2.5&test_boolean=1&test_date=2020-10-10&test_time=10:10:10&test_datetime=2020-10-10%2010:10:10&test_sql_relation_one_to_one=2&test_sql_relation_one_to_many=%5B%221%22,%223%22%5D&test_relation_table_column=3&test_data_source=1&test_up_column=2&test_subscriber=takip&test_validation=2&test_collective_info=say%C4%B1&state=1&test_point=POINT(500296.350474408%204360042.94700808)&test_multipoint=MULTIPOINT(501674.98858913%204365025.16423775,542155.416657117%204401803.22361515,543694.982470102%204294156.00819784)&test_linestring=LINESTRING(539791.710624154%204403666.14895695,499309.862738857%204365496.13876998,545620.665155891%204291313.92653222)&test_multilinestring=MULTILINESTRING((536510.316174699%204400836.37635186,497890.720261438%204365025.26999397,547561.916968295%204286092.32242632),(412503.786730654%204546667.64369013,436116.984720836%204499808.44516787,495613.299361167%204515259.17159379))&test_polygon=POLYGON((483707.616198839%204368810.28709186,484163.146917329%204360328.29597901,516830.412430472%204356558.07768076,516332.09838222%204368339.44195123,483707.616198839%204368810.28709186))&test_multipolygon=MULTIPOLYGON(((495257.053854474%204312007.25434016,496199.087450403%204290158.57482792,608982.916939999%204285193.17522843,593342.246740307%204314454.42764108,495257.053854474%204312007.25434016),(470762.108303432%204383829.74933306,464072.777163527%204365976.68073683,529365.554824327%204360293.92706668,530231.35716293%204381012.9571839,470762.108303432%204383829.74933306),(520686.278495116%204419441.09549696,518846.613778572%204403512.88315985,576279.845450729%204402931.00869838,575080.463944259%204429134.6023466,520686.278495116%204419441.09549696)))&test_string2=string&column_set_id=0&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    
    
    public function testShowFilterForTestTable()
    {
        //Create filter
        $url = 'tables/data_filters/store?name_basic=Filter%20test%20for%20show&data_filter_type_id=5&sql_code=test.id%20%3E%205&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
                
        
        //Add Filter In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $temp = json_decode($authGroup->auths);
        array_push($temp, 'filters:test:show:'.$id);
        DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
    }
    
    public function testShowRecordFromTestTable()
    {
        $url = 'tables/test/1?params=%7B%22column_set_id%22:%220%22%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testDeleteRecordShowAuthAndFilter()
    {
        //Remove Filter and Show Auth In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        
        $temp = [];
        foreach($auths as $auth)
            if($auth != 'filters:test:show:'.$id && $auth != 'tables:test:shows:0')
                array_push($temp, $auth);
            
        $temp = DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
        
        $this->assertEquals(TRUE, $temp);
    }
    
    public function testShowRecordFromTestTable1()
    {
        $url = 'tables/test/1?params=%7B%22column_set_id%22:%220%22%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    
    
    public function testDeleteRecordCreateAuth()
    {
        //Remove Filter and Show Auth In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        
        $temp = [];
        foreach($auths as $auth)
            if($auth != 'tables:test:creates:0')
                array_push($temp, $auth);
            
        $temp = DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
        
        $this->assertEquals(TRUE, $temp);
    }
    
    public function testCreateRecordFormTestTable()
    {
        $url = 'tables/test/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    public function testStoreRequestTestTable()
    {
        $url = 'tables/test/store?test_types_id=2&test_types_ids=%5B%223%22,%221%22%5D&test_text=text&test_json=%7B%22key%22:%22value%22%7D&test_integer=2&test_float=2.5&test_boolean=1&test_date=2020-10-10&test_time=10:10:10&test_datetime=2020-10-10%2010:10:10&test_sql_relation_one_to_one=2&test_sql_relation_one_to_many=%5B%221%22,%223%22%5D&test_relation_table_column=3&test_data_source=1&test_up_column=2&test_subscriber=takip&test_validation=2&test_collective_info=say%C4%B1&state=1&test_point=POINT(500296.3504744077%204360042.947008081)&test_multipoint=MULTIPOINT((501674.9885891296%204365025.164237745),(542155.4166571172%204401803.223615149),(543694.9824701024%204294156.0081978375))&test_linestring=LINESTRING(539791.7106241542%204403666.148956954,499309.8627388567%204365496.13876998,545620.6651558914%204291313.926532225)&test_multilinestring=MULTILINESTRING((536510.3161746992%204400836.3763518585,497890.72026143764%204365025.2699939655,547561.9169682949%204286092.32242632),(412503.78673065436%204546667.643690126,436116.9847208359%204499808.445167866,495613.29936116654%204515259.171593788))&test_polygon=POLYGON((483707.61619883927%204368810.287091862,484163.1469173294%204360328.295979012,516830.412430472%204356558.077680764,516332.09838221973%204368339.4419512255,483707.61619883927%204368810.287091862))&test_multipolygon=MULTIPOLYGON(((495257.0538544743%204312007.254340158,496199.0874504032%204290158.574827923,608982.9169399985%204285193.175228426,593342.2467403072%204314454.4276410835,495257.0538544743%204312007.254340158),(470762.1083034324%204383829.749333064,464072.77716352727%204365976.680736833,529365.5548243275%204360293.927066676,530231.3571629297%204381012.957183905,470762.1083034324%204383829.749333064),(520686.2784951155%204419441.095496964,518846.6137785723%204403512.883159852,576279.845450729%204402931.008698384,575080.4639442589%204429134.602346605,520686.2784951155%204419441.095496964)))&test_string2=string&column_set_id=0&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    
    
    public function testRecordListForTestTableAuthControl()
    {
        //List have 1 record
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 1);
        
        
        
        //Create filter
        $url = 'tables/data_filters/store?name_basic=Filter%20test%20for%20list&data_filter_type_id=1&sql_code=test.id%20%3E%205&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
        
        
        
        
        //Add Filter In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $temp = json_decode($authGroup->auths);
        array_push($temp, 'filters:test:list:'.$id);
        DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
    }
    
    public function testRecordListForTestTableSearchCount0C()
    {
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url);
        
        $data = $response->getData();
        $this->assertEquals(count($data->data->records), 0);
    }
    
    public function testDeleteListAuthAndFilter()
    {
        //Remove Filter and List Auth In Auth Group
        $id = $this->getLastId('data_filters');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        
        $temp = [];
        foreach($auths as $auth)
            if($auth != 'filters:test:list:'.$id && $auth != 'tables:test:lists:0')
                array_push($temp, $auth);
            
        DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($temp)]);
        
        
        //Return 
        $url = 'tables/test?params=%7B%22page%22:1,%22limit%22:3,%22column_array_id%22:%220%22,%22column_array_id_query%22:%220%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%22id%22,%22test_types_id%22,%22test_types_ids%22,%22test_text%22,%22test_json%22,%22test_integer%22,%22test_float%22,%22test_boolean%22,%22test_date%22,%22test_time%22,%22test_datetime%22,%22test_sql_relation_one_to_one%22,%22test_sql_relation_one_to_many%22,%22test_relation_table_column%22,%22test_data_source%22,%22test_up_column%22,%22test_subscriber%22,%22test_validation%22,%22test_collective_info%22,%22state%22,%22own_id%22,%22user_id%22,%22created_at%22,%22updated_at%22,%22test_point%22,%22test_multipoint%22,%22test_linestring%22,%22test_multilinestring%22,%22test_polygon%22,%22test_multipolygon%22,%22test_string2%22%5D%7D&';
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth');
    }
    
    
    
    
    
    /****    Mission Testing    ****/
    
    public function testMissionCreateForm()
    {
        $url = 'tables/missions/create?params=%7B%22column_set_id%22:%220%22%7D&';
        $this->standartTest($url);
    }
    
    public function testMissionCreate()
    {
        $url = 'tables/missions/store?name=test&cron=&php_code=%3C?php%0A$temp%20=%20%5CDB::table(%27settings%27)-%3Ewhere(%27name%27,%20%27REC_COUNT_PER_PAGE%27)-%3Eupdate(%5B%27value%27%20=%3E%2010%5D);%0Aif($temp)%0A%20%20%20%20$return%20=%20%27ok%27;%0Aelse%0A%20%20%20%20$return%20=%20%27fail%27;%0A?%3E&description=&state=1&column_set_id=0&';
        $this->createAndTest($url);
    }
    
    public function testMissionTrigger()
    {
        $id = $this->getLastId('missions');
        
        $url = 'missions/'.$id;
        $response = $this->standartTest($url, FALSE);        
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'no.auth.for.mission.trigger');
    }
    
    public function testAddMissionAuth()
    {
        //Remove Filter and Show Auth In Auth Group
        $id = $this->getLastId('missions');
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->first();
        $auths = json_decode($authGroup->auths);
        array_push($auths, 'missions:'.$id.':0:0');            
        $temp = DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => json_encode($auths)]);
        
        $this->assertEquals(TRUE, $temp);
    }
    
    public function testMissionTrigger1()
    {
        $id = $this->getLastId('missions');
        
        $url = 'missions/'.$id;
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'ok');
    }
    
    
    
    
    
    /****    Test Related Info Page    ****/
    
    public function testCreateJoinForTestTypesShow()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        $columnId = get_attr_from_cache('columns', 'name', 'test_types_id', 'id');
        
        $url = 'tables/join_tables/store?name_basic=Join%20for%20test&join_table_id='.$tableId.'&join_table_alias=test_table&connection_column_with_alias=test_types.id&join_column_id='.$columnId.'&description=&state=1&column_set_id=0&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateColumnArrayForTestTypesShow()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'test_types', 'id');
        $joinId = $this->getLastId('join_tables');
        
        $url = 'tables/column_arrays/store?name_basic=Test%20join%20data%20in%20show&column_array_type_id=2&table_id='.$tableId.'&column_ids=%5B%5D&join_table_ids=%5B%22'.$joinId.'%22%5D&join_columns=test_text,%20test_string2&description=&state=1&column_set_id=0&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testCreateColumnSetForTestTypesShow()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'test_types', 'id');
        $columnArrayId = $this->getLastId('column_arrays');
        
        $url = 'tables/column_sets/store?name_basic=test%20types%20show&table_id='.$tableId.'&column_set_type_id=1&column_array_ids=%5B%22'.$columnArrayId.'%22%5D&description=&state=1&column_set_id=0&';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
    }
    
    public function testChangeAuthForTestTypeShow()
    {
        //Remove Filter and List Auth In Auth Group
        $columnSetId = $this->getLastId('column_sets');//5
        $columnArrayId = $this->getLastId('column_arrays');//
        
        $authGroup = DB::table('auth_groups')->orderBy('id', 'desc')->limit(1)->offset(1)->first();
        $auths = json_decode($authGroup->auths);
        $auths = str_replace('tables:test_types:shows:0', 'tables:test_types:shows:'.$columnSetId, $auths);
        DB::table('auth_groups')->where('id', $authGroup->id)->update(['auths' => $auths]);
        
        
        
        $url = 'tables/test_types/2?params=%7B%22column_set_id%22:%225%22%7D&';
        $response = $this->standartTest($url);        
        $data = $response->getData();
        $this->assertEquals($data->data->column_set->column_arrays[0]->tree, $columnSetId.':'.$columnArrayId);
        
        $url = 'tables/test_types/2/getRelationTableData/'.$columnSetId.':'.$columnArrayId.'?params=%7B%22page%22:1,%22limit%22:%222%22,%22column_array_id%22:%228%22,%22column_array_id_query%22:%228%22,%22sorts%22:%7B%7D,%22filters%22:%7B%7D,%22edit%22:true,%22columns%22:%5B%5D%7D&';
        $response = $this->standartTest($url);        
    }
    
    
    
    public function testDeteleTestTeble()
    {
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        
        $url = 'tables/tables/'.$tableId.'/delete?';
        $response = $this->standartTest($url);
        $data = $response->getData();
        $this->assertEquals($data->data->message, 'success');
        
        
        $deletedTestId = get_attr_from_cache('tables', 'name', 'deleted_test', 'id');
        $this->assertEquals(is_numeric($deletedTestId), TRUE);
    }
}