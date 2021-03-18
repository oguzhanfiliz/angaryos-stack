<?php

namespace App\Policies;

use App\BaseModel;
use App\User;

use App\Libraries\ColumnClassificationLibrary;

trait UserPolicyTrait
{    
    private function columnSetIsHaveSingleColumn($tableName, $columnSetId, $singleColumnName)
    {
        if($columnSetId == 0)
        {
            $columnIds = get_attr_from_cache('tables', 'name', $tableName, 'column_ids');
            if($columnIds == NULL) return FALSE;
            $columnIds = json_decode($columnIds);
        }
        else
        {
            $columnArrayIds = get_attr_from_cache('column_sets', 'id', $columnSetId, 'column_array_ids');
            $columnArrayIds = json_decode($columnArrayIds);
            
            $columnIds = [];
            foreach($columnArrayIds as $columnArrayId)
            {
                $columnArray = get_attr_from_cache('column_arrays', 'id', $columnArrayId, '*');
                if($columnArray == NULL) return FALSE;
                
                $columnArrayTypeName = get_attr_from_cache('column_array_types', 'id', $columnArray->column_array_type_id, 'name');
                if($columnArrayTypeName != 'direct_data') continue;
                
                $temp = json_decode($columnArray->column_ids);
                $columnIds = array_merge($columnIds, $temp);
            }
        }
        
        $singleColumnId = get_attr_from_cache('columns', 'name', $singleColumnName, 'id');
        return in_array($singleColumnId, $columnIds);
    }
    
    public function columnIsPermittedForQuery(User $user, $column_name)
    {
        $control = $this->columnIsPermittedForList($user, $column_name);
        if($control) return TRUE;
        
        $table = 'column_arrays';
        return $this->columnIsPermited($user, $column_name, $table, 'queries');
    }
    
    public function columnIsPermittedForList(User $user, $column_name)
    {
        $table = 'column_arrays';
        return $this->columnIsPermited($user, $column_name, $table, 'lists');
    }

    private function columnIsPermited($user, $columnName, $table, $type)
    {
        global $pipe;
        
        if(!isset($user->auths["tables"])) return FALSE;
        if(!isset($user->auths["tables"][$pipe['table']])) return FALSE;
        if(!isset($user->auths["tables"][$pipe['table']][$type])) return FALSE;
        
        foreach($user->auths["tables"][$pipe['table']][$type] as $columnArrayOrSetId)
        {
            if($columnArrayOrSetId == 0)
                $control = $this->columnIsPermitedForAllColumns($columnName);
            else
                $control = $this->columnIsPermitedForColumnArrayOrSet($columnName, $table, $columnArrayOrSetId);
            
            if($control) return TRUE;
        }
        
        return FALSE;
    }
    
    private function columnIsPermitedForAllColumns($columnName)
    {
        global $pipe;
        
        $model = new BaseModel($pipe['table']);
        foreach($model->getAllColumnsFromDB() as $column)
            if($column['name'] == $columnName)
                return TRUE;
            
        return FALSE;
    }
    
    private function columnIsPermitedForColumnArrayOrSet($columnName, $table, $columnArrayOrSetId)
    {
        //$model = new BaseModel($table);
        //$model = $model->find($columnArrayOrSetId);

        $model = get_attr_from_cache($table, 'id', $columnArrayOrSetId, '*');
        
        $arr = helper('divide_select', $model->join_columns);
        foreach($arr as $c)
        {
            if(strlen(trim($c)) == 0) continue;
            
            $temp = helper('get_column_data_for_joined_column', $c);
            if($temp[1] == $columnName)
                return TRUE;
        }

        $columnIds = json_decode($model->column_ids);
        //foreach($model->getRelationData('column_ids') as $column)
        foreach($columnIds as $columnId)
        {
            $tempColumnName = get_attr_from_cache('columns', 'id', $columnId, 'name');
            if($tempColumnName == $columnName)
                    return TRUE;
        }
            
        return FALSE;
    }
    
    public function treeIsPermittedForRelationTableData(User $user, $tree)
    {
        $tree = explode(':', $tree);
        if(count($tree) != 2) return FALSE;
        
        global $pipe;
        
        if(!in_array($tree[0], $user->auths['tables'][$pipe['table']]['shows']))
            return FALSE;
        
        $column_set = get_attr_from_cache('column_sets', 'id', $tree[0], '*');
                        
        /*if(!in_array($tree[1], $column_set->column_group_ids))
            return FALSE;
        
        $column_group = get_attr_from_cache('column_groups', 'id', $tree[1], '*');
        $column_group->fillVariables();

        if(!in_array($tree[2], $column_group->column_array_ids))
            return FALSE;
        
        $column_array = get_attr_from_cache('column_arrays', 'id', $tree[2], '*');
        $column_array->fillVariables();         */
        
        $ids = json_decode($column_set->column_array_ids);
        if(!in_array($tree[1], $ids))
            return FALSE;
        
        $column_array = get_attr_from_cache('column_arrays', 'id', $tree[1], '*');
        $typeName = get_attr_from_cache('column_array_types', 'id', $column_array->column_array_type_id, 'name');
        
        if($typeName != 'table_from_data')
            return FALSE;
        
        return TRUE;
    }
    
    public function columnSetOrArrayIsPermitted($user, $columnSetOrArrayId, $type)
    {
        if(!is_numeric($columnSetOrArrayId)) return FALSE;
        
        global $pipe;
        
        if(!isset($user->auths['tables'])) return FALSE;
        if(!isset($user->auths['tables'][$pipe['table']])) return FALSE;
        if(!isset($user->auths['tables'][$pipe['table']][$type])) return FALSE;
        
        if(!in_array((int)$columnSetOrArrayId, $user->auths['tables'][$pipe['table']][$type]))
            return FALSE;
        
        return TRUE;
    }
    
    public function recordPermitted($record, $type, $columnSetId = 0)
    {
        switch ($type) 
        {
            case 'delete':
                $permitName = '_is_deletable';
                break;
            case 'clone':
                $permitName = '_is_showable';
                break;
            case 'restore':
                $permitName = '_is_restorable';
                break;
            case 'export':
                $permitName = '_is_exportable';
                break;
            case 'update':
                $permitName = '_is_editable';
                break;
            default: dd('invalid type: ' . $type);
        }
        
        $permissions = $this->getRecordPermissions($record, $type, $columnSetId);
        
        if($permissions == NULL) return FALSE;
        
        if(isset($permissions->{$permitName}) && !$permissions->{$permitName}) return FALSE;
        return TRUE;
    }
    
    private function getRecordPermissions($record, $type, $columnSetId)
    {
        global $pipe;

        if(get_class($record) == 'stdClass')
        {
            $model = new BaseModel($pipe['table']);
            $record = $model->find($record->id);
        }
        
        $model = $record->getQuery();
        $model = $model->selectRaw($record->getTable().'.*');

        $columnSet = $record->getColumnSet($model, $columnSetId, TRUE);
        $columns = $record->getColumnsFromColumnSet($columnSet);

        $record->addJoinsWithColumns($model, $columns);
        
        $record->addFilters($model, $record->getTable(), $type);        
        $model->where($record->getTable().'.id', $record->id); 
        
        $model->groupBy($pipe['table'].'.id');

        return $model->first();
    }

    private function getColumnNamesFromColumnArray($columnArrayId)
    {
        global $pipe;

        $ext = [];
        if($columnArrayId == 0)
            $json = get_attr_from_cache('tables', 'name', $pipe['table'], 'column_ids');
        else
        {
            $temp = get_attr_from_cache('column_arrays', 'id', $columnArrayId, '*');

            $json = $temp->column_ids;

            if(strlen($temp->join_columns) > 0)
            {
                foreach(helper('divide_select', $temp->join_columns) as $select)
                {
                    $select = explode(' as ', $select);
                    array_push($ext, trim(last($select)));
                }
            }
        }

        $columnNames = [];
        foreach(json_decode($json) as $cId)
            array_push($columnNames, get_attr_from_cache('columns', 'id', $cId, 'name'));

        if(count($ext) > 0) $columnNames = array_merge($columnNames, $ext);
        
        return $columnNames;
    }

    private function isColumnsInColumnArray($columnArrayId, $columns)
    {
        if(count((array)$columns) == 0) return TRUE;
        
        $columnNames = $this->getColumnNamesFromColumnArray($columnArrayId);

        foreach($columns as $columnName => $column)
            if(!in_array($columnName, $columnNames)) 
                return FALSE;

        return TRUE;
    }

    private function getRelationColumnsForSelectDataControl($columnSetId, $params)
    {
        $relationColumns = [];

        if($columnSetId == 0)
        {
            $columnIds = json_decode(get_attr_from_cache('tables', 'name', $params->tableName, 'column_ids'));
            foreach($columnIds as $columnId)
            {
                $temp = get_model_from_cache('columns', 'id', $columnId, '*');
                if(strlen($temp->column_table_relation_id) == 0) continue;

                $relationColumns[$temp->name] = $temp;
            }
        }
        else
        {
            $columnArrayIds = json_decode(get_attr_from_cache('column_sets', 'id', $columnSetId, 'column_array_ids'));
            foreach($columnArrayIds as $columnArrayId)
            {
                $columnIds = json_decode(get_attr_from_cache('column_arrays', 'id', $columnArrayId, 'column_ids'));
                
                foreach($columnIds as $columnId)
                {
                    $temp = get_model_from_cache('columns', 'id', $columnId, '*');
                    if(strlen($temp->column_table_relation_id) == 0) continue;

                    $relationColumns[$temp->name] = $temp;
                }
            }
        }

        return $relationColumns;
    }

    private function selectDataControl($user, $columnSetId)
    {
        if(\Request::segment(6) == 'create') return;
        if(\Request::segment(7) == 'edit') return;

        $params = helper('get_null_object'); 
        $params->user = $user;

        $params->ctrl = new \App\Http\Controllers\Api\V1\TableController();

        global $pipe;
        $params->tableName = $pipe['table'];
        $params->table = new \App\BaseModel($params->tableName);//get_model_from_cache('tables', 'name', $params->tableName);

        $relationColumns = $this->getRelationColumnsForSelectDataControl($columnSetId, $params);
        foreach($relationColumns as $column)
        {
            $params->column = $column;            
            $params->relation = get_attr_from_cache('column_table_relations', 'id', $params->column->column_table_relation_id,'*'); 

            ColumnClassificationLibrary::relation(   $this, 
                                                    __FUNCTION__, 
                                                    $params->column, 
                                                    $params->relation, 
                                                    $params);
        }
    }

    public function selectDataControlForBase($params)
    {
        $filterIds = @$params->user->auths['filters'][$params->tableName]['selectColumnData'];
        if(!$filterIds) return;

        foreach($filterIds as $filterId)
        {
            $sqlOrJson = get_attr_from_cache('data_filters', 'id', $filterId, 'sql_code');

            $decoded = @json_decode($sqlOrJson);
            if(!$decoded) $this->selectColumnDataValidate($params);
            else
            {
                dd_live(FALSE, 'adam hakketen bu filreye girecek mi? yani bu filtre bu tablo ve kolon aramasi için geçerli mi?', $sqlOrJson, @json_decode($sqlOrJson));
                //öyleyse request simule et 
                //https://kamu.kutahya.gov.tr/api/v1/15aJOPXMtgNQsprnd4921/tables/kullanici_mesaileri/getSelectColumnData/personel_id?search=5129 
            }
        }
    }

    public function selectDataControlForJoinTableIds($params)
    {
        return $this->selectDataControlForBase($params);
    }

    public function selectDataControlForTableIdAndColumnNames($params)
    {
        return $this->selectDataControlForBase($params);
    }

    public function selectDataControlForTableIdAndColumnIds($params)
    {
        return $this->selectDataControlForBase($params);
    }
    
    public function selectDataControlForRelationSql($params)
    {
        return $this->selectDataControlForBase($params);
    }

    public function selectDataControlForDataSource($params)
    {
        global $pipe;

        $filterIds = @$params->user->auths['filters'][$params->tableName]['selectColumnData'];
        if(!$filterIds) return;

        \Request::merge(['search' => '***']); 
        $params->ctrlParams = $params->ctrl->getValidatedParamsForSelectColumnData($params->table, $params->column); 

        foreach($filterIds as $filterId)
        {
            $data = \Request::input($params->column->name);
            if(!$data) return;
            
            $control = @json_decode($data);
            if($control)
            {
                foreach($control as $item)
                {
                    $finded = FALSE;
                    
                    \Request::merge(['search' => $item]);
                    $params->ctrlParams->search = $item;

                    //$pipe['addedJoins'] = [];
                    $response = \Event::dispatch('record.selectColummnData.requested', [$params->column, $params->ctrlParams])[0];
                    foreach($response['results'] as $rec)
                        if($rec['id'] == $item)
                        {
                            $finded = TRUE;
                            break;
                        }

                    if(!$finded) custom_abort('no.auth.for.column('.$params->column->name.').data:'.$item);
                }
            }
            else
            {
                dd('single rs data', $data, $control);
            }
        }
    }



    public function selectColumnDataValidate($params)
    {
        \Request::merge(['search' => '***']); 
        $params->ctrlParams = $params->ctrl->getValidatedParamsForSelectColumnData($params->table, $params->column); 
        $params->ctrlParams->limit = 10000;

        ColumnClassificationLibrary::relationDbTypes(   $this, 
                                                        __FUNCTION__, 
                                                        $params->column, 
                                                        NULL, 
                                                        $params);
    }

    public function selectColumnDataValidateForOneToOne($params)
    {
        global $pipe;

        $data = \Request::input($params->column->name);
        if(strlen($data) == 0 || $data == '[]') return;

        \Request::merge(['search' => $data]);
        $params->ctrlParams->search = $data;

        //$pipe['addedJoins'] = [];
        $response = \Event::dispatch('record.selectColummnData.requested', [$params->column, $params->ctrlParams])[0];

        foreach($response['results'] as $rec)
            if($rec['id'] == $data) return;
        
        custom_abort('no.auth.for.column('.$params->column->name.').data:'.$data);
    }

    public function selectColumnDataValidateForOneToMany($params)
    {
        global $pipe;
        
        $data = \Request::input($params->column->name);
        if(strlen($data) == 0 || $data == '[]') return;

        $control = @json_decode($data);
        if($control)
        {
            foreach($control as $item)
            {
                $finded = FALSE;
                
                \Request::merge(['search' => $item]);
                $params->ctrlParams->search = $item;

                //$pipe['addedJoins'] = [];
                $response = \Event::dispatch('record.selectColummnData.requested', [$params->column, $params->ctrlParams])[0];
                foreach($response['results'] as $rec)
                    if($rec['id'] == $item)
                    {
                        $finded = TRUE;
                        break;
                    }

                if(!$finded) custom_abort('no.auth.for.column('.$params->column->name.').data:'.$item);
            }
        }
        else
        {
            dd('single2 rs data', $data, $control);
        }
    }
}
