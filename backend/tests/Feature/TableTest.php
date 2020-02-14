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
        $url = 'tables/column_table_relations/store?name_basic=Test%20Types%20Sql%20Relation&relation_source_column_id=&relation_display_column_id=&join_table_ids=%5B%5D&relation_sql=select%20*%20from%20test_types&relation_source_column=id&relation_display_column=name&column_data_source_id=&up_column_id=&description=&state=1&column_set_id=0&in_form_column_name=column_table_relation_id&';
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
    
    
    //
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
        $id = get_attr_from_cache('columns', 'name', 'test_string', 'id');
        $typeId = get_attr_from_cache('column_db_types', 'name', 'text', 'id');
        
        $url = 'tables/columns/'.$id.'/update?display_name=Test%20String&name=test_string2&column_db_type_id='.$typeId.'&column_gui_type_id=1&up_column_id=&column_table_relation_id=&subscriber_ids=%5B%5D&column_validation_ids=%5B%5D&column_gui_trigger_ids=%5B%5D&column_collective_info_id=&default=&state=1&column_set_id=2&';
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
        $id = DB::table('settings')->where('name', 'SHOW_DELETED_TABLES_AND_COLUMNS')->first()->id;
        $url = 'tables/settings/'.$id.'/update?name=SHOW_DELETED_TABLES_AND_COLUMNS&value=1&description=&state=1&column_set_id=0&';
        $this->standartTest($url);
        
        
        
        
        $tableId = get_attr_from_cache('tables', 'name', 'test', 'id');
        
        $id = get_attr_from_cache('columns', 'name', 'deleted_test_string2', 'id');
        $archive = DB::table('tables_archive')->where('record_id', $tableId)->whereRaw('column_ids @> \''.$id.'\'::jsonb')->first();
        
        $url = 'https://192.168.10.185/api/v1/1111111111111111d1/tables/tables/'.$archive->id.'/restore?';
        
        $data = $this->getWithCurl($url);
        $this->controlObjectIsSuccess($data);
        
        
        $id = DB::table('settings')->where('name', 'SHOW_DELETED_TABLES_AND_COLUMNS')->first()->id;
        $url = 'tables/settings/'.$id.'/update?name=SHOW_DELETED_TABLES_AND_COLUMNS&value=0&description=&state=1&column_set_id=0&';
        $this->standartTest($url);
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
}