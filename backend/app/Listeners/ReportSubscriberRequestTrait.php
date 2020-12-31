<?php

namespace App\Listeners;

use DB;

trait ReportSubscriberRequestTrait 
{
    public function getDataForReport($model, $params)
    {
        if($params->record_id == 0) return $this->getDataForTableReport($model, $params);
        else return $this->getDataForRecordReport($model, $params);
    }

    public function getDataForRecordReport($model, $params) 
    {
        $params->column_array_id = $this->GetColumnArrayId($params);
        
        global $pipe;
        
        $except = ['tables', 'columns'];
        
        $params = $this->getModelForTableReport($model, $params);
        $params->model->where($model->getTable().'.id', $params->record_id);

        if(in_array($model->getTable(), $except) && $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] != '1')
            $params->model->where($model->getTable().'.name', 'not ilike', 'deleted\_%');

        $records = $params->model->get();
        $records = $model->updateRecordsDataForResponse($records, $params->columns);
        $records = $this->UpdateRecordsDataForReport($records, $params);
        $record = $records[0];

        $tableInfo = $model->getTableInfo($params->table_name);
        
        $columns = $model->getFilteredColumns($params->columns);
        
        $type = 'standart';//grid, mapped
        $startRow = 1;
        $startCol = 'A';
        $activeSheet = 0;
        $overrideColumnSort = FALSE;
        
        if($params->report_id > 0) 
        {
            $report = get_attr_from_cache('reports', 'id', $params->report_id, '*');
            eval(helper('clear_php_code', $report->php_code));
        }
        
        return 
        [
            'table_info' => $tableInfo,
            'record' => $record,
            'records' => $records,
            'collectiveInfos' => @$collectiveInfos, 
            'columns' => $columns,
            'type' => $type,
            'startRow' => $startRow,
            'startCol' => $startCol,
            'overrideColumnSort' => $overrideColumnSort,
            'activeSheet' => $activeSheet,
            'gridData' => @$gridData,
            'report' => @$report
        ];
    }
    
    public function getDataForTableReport($model, $params) 
    {
        $params->column_array_id = $this->GetColumnArrayId($params);
        
        global $pipe;
        
        $except = ['tables', 'columns'];
        
        $params = $this->getModelForTableReport($model, $params);
        
        if(in_array($model->getTable(), $except) && $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] != '1')
            $params->model->where($model->getTable().'.name', 'not ilike', 'deleted\_%');
        
        $collectiveInfos = $model->getCollectiveInfos($params->model, $params->columns);
        
        $records = $params->model->get();
        $records = $model->updateRecordsDataForResponse($records, $params->columns);
        $records = $this->UpdateRecordsDataForReport($records, $params);
        
        $tableInfo = $model->getTableInfo($params->table_name);
        
        $columns = $model->getFilteredColumns($params->columns);
        
        $type = 'standart';//grid, mapped
        $startRow = 1;
        $startCol = 'A';
        $activeSheet = 0;
        $overrideColumnSort = FALSE;
        
        if($params->report_id > 0) 
        {
            $report = get_attr_from_cache('reports', 'id', $params->report_id, '*');
            eval(helper('clear_php_code', $report->php_code));
        }
        
        return 
        [
            'table_info' => $tableInfo,
            'records' => $records,
            'collectiveInfos' => $collectiveInfos, 
            'columns' => $columns,
            'type' => $type,
            'startRow' => $startRow,
            'startCol' => $startCol,
            'activeSheet' => $activeSheet,
            'overrideColumnSort' => $overrideColumnSort,
            'gridData' => @$gridData,
            'report' => @$report
        ];
    }
    
    public function GetColumnArrayId($params)
    {
        if($params->report_id == 0) return $params->column_array_id;
        
        $report = get_attr_from_cache('reports', 'id', $params->report_id, '*');        
        return $report->column_array_id;
    }
    
    public function UpdateRecordsDataForReport($records, $params)
    {
        $columns = $params->columns;
        
        $jsonColumns = [];
        
        foreach($columns as $column)
        {
            $column->dbTypeName = get_attr_from_cache('column_db_types', 'id', $column->column_db_type_id, 'name');
            $column->guiTypeName = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
             
            if(strstr($column->dbTypeName, 'json')) $jsonColumns[$column->name] = [$column->dbTypeName, $column->guiTypeName];
            
            foreach($records as $i => $record)
            {
                if($column->dbTypeName == 'date' && strlen(trim($record->{$column->name})) > 0)
                {
                    $temp = explode('-', $record->{$column->name});
                    $records[$i]->{$column->name} = $temp[2].'.'.$temp[1].'.'.$temp[0];
                }
                else if($column->guiTypeName == 'richtext')
                {
                    $records[$i]->{$column->name} = strip_tags($record->{$column->name});
                    $records[$i]->{$column->name} = str_replace('&nbsp;', ' ', $records[$i]->{$column->name});
                }
                else if(strstr($column->guiTypeName, 'money:'))
                {
                    $records[$i]->{$column->name} = str_replace(',', '.', $records[$i]->{$column->name});
                    //$records[$i]->{$column->name} = number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $records[$i]->{$column->name})),2);
                    //$records[$i]->{$column->name} .= ' ' . strtoupper(explode(':', $column->guiTypeName)[1]);
                }
            }
        }
        
        foreach($records as $i => $record)
            foreach($jsonColumns as $columnName => $types)
                $records[$i]->{$columnName} = $this->UpdateRecordJsonColumnDataForReport($records[$i]->{$columnName}, $types);
    
        return $records;
    }
    
    public function UpdateRecordJsonColumnDataForReport($json, $types)
    {
        if(strlen($json) == 0) return '';
        if($json == '[]') return '';
        
        $return = '';

        $json = json_decode($json);
        switch($types[1])
        {
            case 'json':
            case 'jsonb':
            case 'multiselect':
            case 'multiselectdragdrop':
                foreach($json as $item) $return .=  $item->display .', ';
                $return = substr($return, 0, -2);    
                break;
            case 'files':
                foreach($json as $item) $return = helper('get_file_url', $item) . ', ';
                $return = substr($return, 0, -2);    
                break;
            case 'jsonviewer:newpage':
                $return = json_encode($json);
                break;
            default: custom_abort('undefined.json.column.gui.type:'.$types[1]);
        }
        
        return $return;
    }
    
    public function getModelForTableReport($model, $params)
    {
        $params->model = $model->getQuery();
        
        $params->columns = $model->getColumns($params->model, 'column_arrays', $params->column_array_id);
        
        $model->addJoinsWithColumns($params->model, $params->columns);
        $model->addSorts($params->model, $params->columns, $params->sorts);
        $model->addWheres($params->model, $params->columns, $params->filters);
        $model->addSelects($params->model, $params->columns);
        $model->addFilters($params->model, $params->table_name);
        
        $params->model->addSelect($params->table_name.'.id');

        $columnIds = get_attr_from_cache('tables', 'name', $params->table_name, 'column_ids');
        $columnIds = json_decode($columnIds);
        foreach($columnIds as $columnId)
        {
            $columnName = get_attr_from_cache('columns', 'id', $columnId, 'name');
            $params->model->addSelect($params->table_name.'.'.$columnName.' as '.$columnName.'_orj');
        }

        $standartColumns = ['id', 'own_id', 'user_id', 'created_at', 'updated_at', 'state'];
        foreach($standartColumns as $columnName)
            $params->model->groupBy($params->table_name.'.'.$columnName);

        foreach($params->columns as $column)
            if(!isset($column->select_raw))
                $params->model->groupBy($params->table_name.'.'.$column->name);
        
        return $params;
    }
}
