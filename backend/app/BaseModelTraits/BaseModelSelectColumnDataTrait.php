<?php

namespace App\BaseModelTraits;

use App\Libraries\ColumnClassificationLibrary;
use App\BaseModel;
use DB;

trait BaseModelSelectColumnDataTrait 
{    
    private $deletables = ['tables', 'columns'];
    
    public function getSelectColumnData($params)
    {
        $control = $this->getUpColumnControl($params);
        if($control) return $control;
        
        $temp = helper('get_null_object');
        $temp->page = $params->page;
        $temp->search = $params->search;
        $temp->up_column_name = $params->upColumnName;
        $temp->up_column_data = $params->upColumnData;
        $temp->column = $this;
        $temp->record_per_page = $params->limit;
        
        return ColumnClassificationLibrary::relation(  $this, 
                                                        __FUNCTION__,
                                                        $this, 
                                                        NULL, 
                                                        $temp);
    }

    private function getFirstJoinTableAliasForSelectColumn($relationTable)
    {
        if(is_string($relationTable->join_table_ids))
            $joinTableIds = json_decode($relationTable->join_table_ids);
        else
            $joinTableIds = $relationTable->join_table_ids;

        return get_attr_from_cache('join_tables', 'id', $joinTableIds[0], 'join_table_alias');
    }
    
    public function getSelectColumnDataForJoinTableIds($params)
    {
        global $pipe;
        
        $relationTable = $params->column->getRelationData('column_table_relation_id');
        $table = $relationTable->getRelationData('relation_table_id');
        
        $temp = new BaseModel($table->name);
        $model = $temp->getQuery();
        
        $temp->addJoinsWithColumns($model, [$params->column], TRUE);


        $alias = $this->getFirstJoinTableAliasForSelectColumn($relationTable);
        
        $source = $relationTable->relation_source_column;
        if(!strstr($source, '.')) $source = $table->name.'.'.$source;      
        
        $display = $relationTable->relation_display_column;
        if(!strstr($display, '.')) $display = $table->name.'.'.$display; 
        
        $source = str_replace($alias.'.', $table->name.'.', $source);
        $display = str_replace($alias.'.', $table->name.'.', $display);

        
        $model->addSelect(DB::raw($source.' as source'));        
        $model->addSelect(DB::raw($display.' as display'));
        
        
        $model->where(function ($query) use($source, $display, $params)
        {
            $query->whereRaw($source.'::text ilike \'%'.$params->search.'%\'');
            $query->orWhereRaw($display.'::text ilike \'%'.$params->search.'%\'');
        });        
        
        $temp->addFilters($model, $table->name, 'selectColumnData');
        
        $sourceSpace = $this->getSourceSpaceFromUpColumn($params);
        if($sourceSpace != FALSE)
            $model->whereIn($source, $sourceSpace);
        
        if(in_array($table->name, $this->deletables) && $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] != '1')
            $model->where($table->name.'.name', 'not like', 'deleted\_%');
        
        $offset = ($params->page - 1) * $params->record_per_page;
        $params->count = $model->count();
        
        $params->records = $model->limit($params->record_per_page)->offset($offset)->get();
        
        $params->relation_source_column_name = 'source';
        $params->relation_display_column_name = 'display';
        
        return $this->getSelectColumnDataFromRecords($params);
    }
    
    public function getSelectColumnDataForRelationSql ($params)
    {
        $sql = ' from ('.$params->relation->relation_sql.') as main_table where ';
        $sql .= ' ('. $params->relation->relation_display_column.'::text ilike \'%'.$params->search.'%\' ';
        $sql .= ' or '.$params->relation->relation_source_column.'::text ilike \'%'.$params->search.'%\' )';
        
        global $pipe;
        if(($pipe['table'] == 'tables' || $pipe['table'] == 'columns') && $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] != '1')
            $sql .= ' and name::text not like \'deleted\_%\' )';
        
        $sourceSpace = $this->getSourceSpaceFromUpColumn($params);
        if($sourceSpace != FALSE)
        {
            dd('up column kontrol getSelectColumnDataForRelationSql');
            //$model->whereIn($sourceColumn->name, $sourceSpace);
        }
        
        
        $params->count = DB::select('select count(*) '.$sql)[0]->count;
        
        $sql .= 'order by ' . $params->relation->relation_source_column . ' limit '.$params->record_per_page.' offset '.(($params->page - 1) * $params->record_per_page );
        $params->records = DB::select('select * '.$sql);
        
        $params->relation_source_column_name = $params->relation->relation_source_column;
        $params->relation_display_column_name = $params->relation->relation_display_column;
        
        return $this->getSelectColumnDataFromRecords($params);
    }
    
    public function getSelectColumnDataForTableIdAndColumnIds($params)
    {
        global $pipe;
        
        $relationTable = $params->column->getRelationData('column_table_relation_id');
        
        $table = $relationTable->getRelationData('relation_table_id');
        $sourceColumn = $relationTable->getRelationData('relation_source_column_id');
        $displayColumn = $relationTable->getRelationData('relation_display_column_id');
        
        $offset = ($params->page - 1) * $params->record_per_page;
        $model = DB::table($table->name)
                ->select($displayColumn->name)
                ->addSelect($sourceColumn->name);
        
        $model->where(function ($query) use($params, $displayColumn, $sourceColumn)
        {
            $query->where($displayColumn->name, 'ilike', '%'.$params->search.'%')
                ->orWhere($sourceColumn->name, 'ilike', '%'.$params->search.'%');
        });
        
        $sourceSpace = $this->getSourceSpaceFromUpColumn($params);
        if($sourceSpace != FALSE)
            $model->whereIn($sourceColumn->name, $sourceSpace);
        
        if(in_array($table->name, $this->deletables) && $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] != '1')
            $model->where($table->name.'.name', 'not like', 'deleted\_%');
        
        $params->count = $model->count();
        $params->records = $model->limit($params->record_per_page)->offset($offset)->get();
        
        $params->relation_source_column_name = $sourceColumn->name;
        $params->relation_display_column_name = $displayColumn->name;
        
        return $this->getSelectColumnDataFromRecords($params);
    }
    
    public function getSelectColumnDataForDataSource($params)
    {
        $sourceSpace = $this->getSourceSpaceFromUpColumn($params);
        if($sourceSpace) dd('upColumn ı aramaya dahil et');
        
        $relation = $params->column->getRelationData('column_table_relation_id');
        $dataSource = $relation->getRelationData('column_data_source_id');
            
        $repository = NULL;
        eval(helper('clear_php_code', $dataSource->php_code));
        
        $data = $repository->searchRecords($params->search, $params->page, $params->record_per_page);
        
        $return['results'] = [];
        foreach($data['records'] as $source => $display)
        {
            $temp['id'] = $source;
            $temp['text'] = $display;
            array_push($return['results'], $temp);
        }
        
        $return['pagination']['more'] = $data['more'];
        
        return $return;
    }
    
    
    
    /****    Up Column Functions    ****/    
    
    private function getSourceSpaceFromUpColumn($params)
    {
        if(strlen($params->up_column_data) == 0) return FALSE;
        
        $params->upColumnRule = get_attr_from_cache('up_columns', 'id', $params->column->up_column_id, '*');
        $upColumnName = get_attr_from_cache('columns', 'id', $params->upColumnRule->column_id, 'name');
        if($upColumnName != $params->up_column_name)
            custom_abort ('invalid.up.column.'.$params->up_column_name);
        
        $data = $params->up_column_data;
        
        $return = NULL;
        eval(helper('clear_php_code', $params->upColumnRule->php_code)); 
        
        return $return;
    }
    
    
    
    /****    Common Functions    ****/
    
    private function getSelectColumnDataFromRecords($params)
    {
        $return['results'] = [];
        foreach($params->records as $rec)
        {
            $temp['id'] = $rec->{$params->relation_source_column_name};
            $temp['text'] = $rec->{$params->relation_display_column_name};
            array_push($return['results'], $temp);
        }
        
        $return['pagination']['more'] = $params->count > ($params->page * $params->record_per_page);
        
        return $return;
    }
    
    private function getUpColumnControl($params)
    {
        if(strlen($params->upColumnName) == 0) return FALSE;
        if(strlen($params->upColumnData) > 0) return FALSE;
        
        $tableId = get_attr_from_cache('tables', 'name', $params->table->getTable(), 'id');
        
        $relation = $this->getRelationData('up_column_id');
        $relation->fillVariables();
        
        if(!in_array($tableId, $relation->table_ids)) return FALSE;
              
        $columnDisplayName = get_attr_from_cache('columns', 'name', $params->upColumnName, 'display_name');
        
        return
        [
            'results' =>
            [
                [
                    'id' => -9999,
                    'text' => 'Önce ' . $columnDisplayName . ' seçiniz'
                ]
            ],
            'pagination' => 
            [
                'more'=> FALSE
            ]
        ];
    }
}