<?php

namespace App\Listeners;

use App\Http\Requests\BaseRequest;

use App\Libraries\ChangeDataLibrary;
use App\Libraries\ColumnClassificationLibrary;

use DB;
use App\BaseModel;

trait TableSubscriberTrait 
{
    /****    List    ****/
    
    public function getDataForList($model, $params) 
    {
        $except = ['tables', 'columns'];
        
        $params = $this->getModelForList($model, $params);
        
        if(in_array($model->getTable(), $except) && SHOW_DELETED_TABLES_AND_COLUMNS != '1')
            $params->model->where($model->getTable().'.name', 'not ilike', 'deleted\_%');
        
        $count = $params->model->count($params->table_name.'.id');
        
        $collectiveInfos = $model->getCollectiveInfos($params->model, $params->columns);
        
        $params->model->limit($params->limit);
        $params->model->offset($params->limit * ($params->page - 1));
        $records = $params->model->get();
        $records = $model->updataDataFromDataSource($records, $params->columns);
        
        $tableInfo = $model->getTableInfo($params->table_name);
        
        $columns = $model->getFilteredColumns($params->columns);
        
        $params->query_columns = $model->getColumns($model->getQuery(), 'column_arrays', $params->column_array_id_query);
        $queryColumns = $model->getFilteredColumns($params->query_columns);
        
        return 
        [
            'table_info' => $tableInfo,
            'records' => $records,
            'collectiveInfos' => $collectiveInfos, 
            'columns' => $columns,
            'query_columns' => $queryColumns,
            'pages' => (int)ceil($count / $params->limit),
            'all_records_count' => $count
        ];
    }
    
    public function getModelForList($model, $params)
    {
        $params->model = $model->getQuery();
        
        $params->columns = $model->getColumns($params->model, 'column_arrays', $params->column_array_id);
        
        $model->addJoinsWithColumns($params->model, $params->columns);
        $model->addSorts($params->model, $params->columns, $params->sorts);
        $model->addWheres($params->model, $params->columns, $params->filters);
        $model->addSelects($params->model, $params->columns);//
        $model->addFilters($params->model, $params->table_name);
        
        $params->model->addSelect($params->table_name.'.id');
        $params->model->groupBy($params->table_name.'.id');
        
        return $params;
    }
    
    
    
    /****    Archive    ****/
    
    public function getDataForArchive($record, $params) 
    {
        $params = $this->getModelForArchive($record, $params);
        
        $count = $params->model->count($params->table_name.'.id');
        
        $params->model->limit($params->limit);
        $params->model->offset($params->limit * ($params->page - 1));
        $records = $params->model->get();
        $records = $record->updataDataFromDataSource($records, $params->columns);
        
        $tableInfo = $record->getTableInfo($params->table_name);
        
        $columns = $record->getFilteredColumns($params->columns);
        
        $params->query_columns = $record->getColumns($record->getQuery(), 'column_arrays', $params->column_array_id_query);
        $queryColumns = $record->getFilteredColumns($params->query_columns);
        
        return 
        [
            'table_info' => $tableInfo,
            'records' => $records,
            'columns' => $columns,
            'query_columns' => $queryColumns,
            'pages' => (int)ceil($count / $params->limit),
            'all_records_count' => $count
        ];
    }
    
    public function getModelForArchive($record, $params)
    {
        $model = new BaseModel($record->getTable().'_archive');
        
        $params->model = $model->getQuery();
        
        $params->columns = $model->getColumns($params->model, 'column_arrays', $params->column_array_id);
        
        $model->addJoinsWithColumns($params->model, $params->columns);
        $model->addSorts($params->model, $params->columns, $params->sorts);
        $model->addWheres($params->model, $params->columns, $params->filters);
        $model->addSelects($params->model, $params->columns);
        
        $params->model->where($params->table_name.'.record_id', $record->id);
        
        $params->model->addSelect($params->table_name.'.id');
        $params->model->groupBy($params->table_name.'.id');
        
        return $params;
    }
    
    
    
    /****    Deleted    ****/
    
    public function getDataForDeleted($params) 
    {
        $params = $this->getModelForDeleted($params);
        
        $count = $params->model->count($params->table_name.'.id');
        
        $params->model->limit($params->limit);
        $params->model->offset($params->limit * ($params->page - 1));
        $records = $params->model->get();
        $records = $params->recordModel->updataDataFromDataSource($records, $params->columns);
        
        $tableInfo = $params->recordModel->getTableInfo($params->table_name);
        
        $columns = $params->recordModel->getFilteredColumns($params->columns);
        
        $params->query_columns = $params->recordModel->getColumns($params->recordModel->getQuery(), 'column_arrays', $params->column_array_id_query);
        $queryColumns = $params->recordModel->getFilteredColumns($params->query_columns);
        
        return 
        [
            'table_info' => $tableInfo,
            'records' => $records,
            'columns' => $columns,
            'query_columns' => $queryColumns,
            'pages' => (int)ceil($count / $params->limit),
            'all_records_count' => $count
        ];
    }
    
    public function getModelForDeleted($params)
    {
        $params->recordModel = new BaseModel($params->table_name);
        
        $params->model = $params->recordModel->getQuery();
        
        $params->columns = $params->recordModel->getColumns($params->model, 'column_arrays', $params->column_array_id);
        
        $params->recordModel->addJoinsWithColumns($params->model, $params->columns);
        $params->recordModel->addSorts($params->model, $params->columns, $params->sorts);
        $params->recordModel->addWheres($params->model, $params->columns, $params->filters);
        $params->recordModel->addSelects($params->model, $params->columns);
        
        $tableName = substr($params->table_name, 0, -8);
        
        $params->model->whereRaw($params->table_name.'.record_id not in (select id from '.$tableName.')');
        
        $params->model->addSelect($params->table_name.'.id');
        $params->model->groupBy($params->table_name.'.id');
        
        return $params;
    }
    
    
    
    /****    Create    ****/
    
    public function getDataForCreate($model, $params) 
    {
        $params = $this->getModelForCreate($model, $params);        
        $tableInfo = $model->getTableInfo($params->table);        
        $columnSet = $model->getFilteredColumnSet($params->columnSet, TRUE);
        
        return 
        [
            'table_info' => $tableInfo,
            'column_set' => $columnSet,
            'gui_triggers' => $params->guiTriggers
        ];
    }
    
    public function getModelForCreate($model, $params)
    {
        $params->model = $model->getQuery();
        
        $params->columnSet = $model->getColumnSet($params->model, $params->column_set_id, TRUE);
        $params->columns = $model->getColumnsFromColumnSet($params->columnSet);
        $params->guiTriggers = $model->getGuiTriggers($params->columns);
        
        return $params;
    }
    
    public function getDataForSelectElement($params, $record)
    {
        $columnName = read_from_response_data('post', 'in_form_column_name');
        if(strlen($columnName) == 0) return;
        
        $singleColumnName = read_from_response_data('post', 'single_column');
        
        if(strlen($singleColumnName) > 0)
        {
            $column = get_attr_from_cache('columns', 'name', $singleColumnName, '*');
            $functionName = __FUNCTION__.'Single';
        }
        else
        {
            $column = get_attr_from_cache('columns', 'name', $columnName, '*');
            $functionName = __FUNCTION__;
        }
        
        if($column == NULL) return;
        
        $temp = helper('get_null_object');
        $temp->record = $record;
        $temp->params = $params;
        $temp->column = $column;

        return ColumnClassificationLibrary::relation($this, $functionName, $column, NULL, $temp);
    }
    
    public function getDataForSelectElementSingleForTableIdAndColumnIds($params)
    {
        $val = $params->record{$params->column->name};
        
        $tableName = get_attr_from_cache('tables', 'id', $params->relation->relation_table_id, 'name');
        $sourceColumnName = get_attr_from_cache('columns', 'id', $params->relation->relation_source_column_id, 'name');
        $displayColumnName = get_attr_from_cache('columns', 'id', $params->relation->relation_display_column_id, 'name');
                
        $rec = DB::table($tableName)
                ->select($sourceColumnName)
                ->addSelect($displayColumnName)
                ->where($sourceColumnName, $val)
                ->first();
        
        return
        [
            'source' => $rec->{$sourceColumnName},
            'display' => $rec->{$displayColumnName}
        ];
    }
    
    public function getDataForSelectElementSingleForBasicColumn($params)
    {
        return $this->getDataForSelectElementForBasicColumn($params);
    }
    
    public function getDataForSelectElementForTableIdAndColumnIds($params)
    {
        $sourceColumnName = get_attr_from_cache('columns', 'id', $params->relation->relation_source_column_id, 'name');
        $displayColumnName = get_attr_from_cache('columns', 'id', $params->relation->relation_display_column_id, 'name');
        
        return
        [
            'source' => $params->record{$sourceColumnName},
            'display' => $params->record{$displayColumnName}
        ];
    }
    
    public function getDataForSelectElementForBasicColumn($params)
    {
        $data = $params->record{$params->column->name};
        
        $dbTypeName = get_attr_from_cache('column_db_types', 'id', $params->column->column_db_type_id, 'name');
        switch($dbTypeName)
        {
            case 'boolean': 
                $data = ($data == '1');
                break;
            default: break;
        } 
        
        return
        [
            'source' => $params->record{$params->column->name},
            'display' => $data
        ];
    }
    
    public function getDataForSelectElementForRelationSql($params)
    {
        return
        [
            'source' => $params->record{$params->relation->relation_source_column},
            'display' => $params->record{$params->relation->relation_display_column}
        ];
    }
    
    public function getDataForSelectElementForDataSource($params)
    {
        //test auths
        //https://192.168.10.185/api/v1/c8n1zcgR1Uet6D4md1/tables/auth_groups/store?name_basic=ccc&in_form_column_name=auths&state=1&column_set_id=0&auths=%5B%22tables:departments:lists:2%22,%22tables:departments:lists:0%22%5D&#
        
        $dataSource = get_attr_from_cache('column_data_sources', 'id', $params->relation->column_data_source_id, '*');
        $repository = NULL;
        eval(helper('clear_php_code', $dataSource->php_code));
        return $repository->getDataForSelectElement($params->record);
    }
    
    
    
    /****    Store    ****/
    
    public function createNewRecord($dataArray, $tableName = NULL)
    {
        if($tableName == NULL)
        {
            global $pipe;
            $tableName = $pipe['table'];
        }
        $dataArray = (Object)$dataArray;
        
        global $pipe;
        if(isset($pipe['overrideRequestDatas']))
            foreach($pipe['overrideRequestDatas'] as $columnName => $columnData)
                $dataArray->{$columnName} = $columnData;
        
        return create_new_record($tableName, $dataArray);
    }
    
    public function validateRecordData($dataArray)
    {
        $dataArray['column_set_id'] = 0;
        
        \Request::merge($dataArray);
        $request = app('App\Http\Requests\BaseRequest');
        return $request->validator->errors()->getMessages();
    }
    
    
    
    /****    Update    ****/
    
    public function updateRecord($record, $dataArray)
    {
        $record->fillVariables();
        
        global $pipe;
        if(isset($pipe['overrideRequestDatas']))
            foreach($pipe['overrideRequestDatas'] as $columnName => $columnData)
                $dataArray->{$columnName} = $columnData;
        
        $keys = array_keys($record->toArray());
        if(!in_array('column_set_id', $keys))
            unset($dataArray->column_set_id);
        
        $columns = $record->getAllColumnsFromDB();
        
        if(copy_record_to_archive($record))
        {
            $helper = new ChangeDataLibrary();
            $record = $helper->updateData($columns, $dataArray, $record);
        }
        
        $record->user_id = \Auth::user()->id;
        
        $record->save();
        
        return $record;
    }
    
    
    
    /****    Edit    ****/
    
    public function getDataForEdit($model, $params) 
    {
        $params = $this->getModelForEdit($model, $params);
        
        $tableInfo = $model->getTableInfo($params->table);
        
        $columnSet = $model->getFilteredColumnSet($params->columnSet, TRUE);
        $columnSet = $this->filterColumnsForSingleColumnForm($columnSet, @$params->single_column_name);
        
        
        $record = $params->model->first();
        
        $record = $model->updataDataFromDataSource($record, $params->columns);
        
        $record = $this->replaceDataForForm($model, $record, $columnSet);
        
        return 
        [
            'table_info' => $tableInfo,
            'record' => $record,
            'column_set' => $columnSet,
            'gui_triggers' => $params->guiTriggers,
        ];
    }
    
    public function filterColumnsForSingleColumnForm($columnSet, $singleColumnName)
    {
        if(strlen($singleColumnName) == 0) return $columnSet;
        
        $clone = helper('clone_object_as_array', $columnSet);
        
        $control = FALSE;
        
        foreach($columnSet->column_arrays as $columnArrayId => $columnArray)
            foreach($columnArray->columns as $columnId => $column) 
            {
                if($column->name == $singleColumnName)
                    $control = TRUE;
                else
                    unset($clone['column_arrays'][$columnArrayId]['columns'][$columnId]);
            }
                
        if(!$control) custom_abort ('no.auth.for.column.'.$singleColumnName);
        
        return helper('clone_object', $clone);
    }
    
    public function getModelForEdit($model, $params)
    {
        $params->model = $model->getQuery();
        
        $params->columnSet = $model->getColumnSet($params->model, $params->column_set_id, TRUE);
        $params->columns = $model->getColumnsFromColumnSet($params->columnSet);
        
        $params->guiTriggers = $model->getGuiTriggers($params->columns);
        
        //$model->addJoinsWithColumns($params->model, $params->columns);
        
        $model->addFilters($params->model, $params->table);        
        $params->model->where($params->table.'.id', $model->id);        
        $params->model->groupBy($params->table.'.id');
        
        return $params;
    }
    
    private function replaceDataForForm($model, $record, $columnSet)
    {
        $dataClasses = ['stdClass', 'App\BaseModel'];
        
        $data = (array)$record;
        
        foreach($columnSet->column_arrays as $columnArray)
            foreach($columnArray->columns as $column) 
                if(strlen($column->column_table_relation_id) == 0)
                    $data[$column->name] = $model->{$column->name};
                else
                {
                    $relationData = $model->getRelationData($column->name);

                    $class = @get_class($relationData); 
                    if(in_array($class, $dataClasses))
                        $relationData = [$relationData];
                    
                    $data[$column->name] = [];
                    if(is_array($relationData))
                        foreach($relationData as $r)
                            array_push($data[$column->name], [
                                'source' => $r->_source_column,
                                'display' => $r->_display_column
                            ]);
                    }
        
        $data['id'] = $model->id;
                    
        return $data;
    }
    
    
    
    /****    Delete    ****/
    
    public function deleteRecord($record)
    {
        $record->fillVariables();
        $except = ['tables', 'columns'];
        
        if(copy_record_to_archive($record))
        {
            if(in_array($record->getTable(), $except))
            {
                DB::table($record->getTable())->where('id', $record->id)->update([
                    'name' => 'deleted_'.$record->name
                ]);
                return TRUE;
            }
            else if($record->delete())
                return TRUE;
        }
        
        return FALSE;
    }
    
    
    
    /****    Show    ****/
    
    public function getDataForShow($model, $params) 
    {
        $params = $this->getModelForShow($model, $params);
        
        $tableInfo = $model->getTableInfo($params->table);
        
        $columnSet = $model->getFilteredColumnSet($params->columnSet);
        
        $record = $params->model->first();
        $record = $model->updataDataFromDataSource($record, $params->columns);
        
        return 
        [
            'table_info' => $tableInfo,
            'record' => $record,
            'column_set' => $columnSet
        ];
    }
    
    public function getModelForShow($model, $params)
    {
        $params->model = $model->getQuery();
        
        $params->columnSet = $model->getColumnSet($params->model, $params->column_set_id);
        $params->columns = $model->getColumnsFromColumnSet($params->columnSet);
        
        $model->addJoinsWithColumns($params->model, $params->columns);
        
        $model->addSelects($params->model, $params->columns);
        $params->model->addSelect($params->table.'.id');
        
        $model->addFilters($params->model, $params->table);
        
        $params->model->where($params->table.'.id', $model->id);
        
        $params->model->groupBy($params->table.'.id');
        
        return $params;
    }
    
    
    
    /****    Restore    ****/
    
    public function restoreRecord($archiveRecord, $record = NULL)
    {
        $archiveRecord->fillVariables();
        $tableName = substr($archiveRecord->getTable(), 0, -8);
        
        if($record != NULL)
        {
            $temp = new BaseModel($tableName);
            $record = $temp->find($record->id);
            
            $control = copy_record_to_archive($record);
            if(!$control) return FALSE;
            
            $data = $archiveRecord->toArray();
            
            unset($data['record_id']);
            unset($data['id']);
            unset($data['created_at']);
            unset($data['own_id']);
            
            $data['user_id'] = \Auth::user()->id;
            $data['updated_at'] = \Carbon\Carbon::now();
            
            foreach($data as $key => $value)
            {
                if(substr($key, -15, 15) == '__relation_data') continue;                
                $record->{$key} = $value;
            }
        }
        else
        {
            $data = $archiveRecord->toArray();
            
            $data['id'] = $data['record_id'];
            unset($data['record_id']);
            
            $data['user_id'] = \Auth::user()->id;
            $data['updated_at'] = \Carbon\Carbon::now();
            
            $createdAt = DB::table($archiveRecord->getTable())
                    ->select('created_at')
                    ->where('record_id', $data['id'])
                    ->orderBy('created_at')
                    ->first()->created_at;
            $data['created_at'] = $createdAt;
            
            $record = new BaseModel($tableName, $data);
        }
        
        $record->save();
        return $record;
    }   
    
    
    
    /****    Auth Assign    ****/
    
    private function authAssign($params)
    {
        if($params['all_user']) 
            return $this->addAuthWithUserList('*', $params['auth_id']);
        
        
        $userIds = $params['user_ids'];

        $temp = DB::table('users')->whereIn('department_id', $params['department_ids'])->get();
        $userIds = $this->mergeUserIdsList($userIds, $temp);

        foreach($params['auths'] as $auth)
        {
            $temp = DB::table('users')->whereRaw('auths @> \''.$auth.'\'::jsonb or auths @> \'"'.$auth.'"\'::jsonb')->get();
            $userIds = $this->mergeUserIdsList($userIds, $temp);
        }

        $this->addAuthWithUserList($userIds, $params['auth_id']);
    }
    
    private function mergeUserIdsList($base, $new)
    {
        foreach($new as $u)
            if(!in_array($u->id, $base))
                array_push($base, $u->id);
            
        return $base;
    }
    
    private function addAuthWithUserList($userIds, $authId)
    {
        $inSql = 'select id from users where auths @> \'"'.$authId.'"\'::jsonb or auths @> \''.$authId.'\'::jsonb';
        
        $sql = 'UPDATE users SET auths  = auths || \'["'.$authId.'"]\'::jsonb ';
        $sql .= ' where id not in ('.$inSql.') ';
        
        if($userIds != '*') $sql .= ' and id in ('.implode(',', $userIds).')';
        
        DB::select($sql);
        
        
        $sql = 'UPDATE users SET auths  = \'["'.$authId.'"]\'::jsonb where (auths::text = \'\') IS NOT FALSE';
        if($userIds != '*') $sql .= ' and id in ('.implode(',', $userIds).')';  
        
        DB::select($sql);        
    }
}
