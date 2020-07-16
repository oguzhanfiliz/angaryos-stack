<?php

namespace App\Listeners;

use Maatwebsite\Excel\Facades\Excel;

use \App\Libraries\ExcelStandartTableCollectionLibrary;
use \App\Libraries\ExcelCustomTableCollectionLibrary;

use DB;

trait ReportSubscriberTrait 
{
    /****        ****/
    
    public function getDataForReport($model, $params)
    {
        if($params->record_id == 0) return $this->getDataForTableReport($model, $params);
        else return $this->getDataForRecordReport($model, $params);
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
            'columns' => $columns
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
            $dbTypeName = get_attr_from_cache('column_db_types', 'id', $column->column_db_type_id, 'name');
        
            if(strstr($dbTypeName, 'json')) array_push($jsonColumns, $column->name);
        }
        
        foreach($records as $i => $record)
            foreach($jsonColumns as $columnName)
                $records[$i]->{$columnName} = $this->UpdateRecordColumnDataForReport($records[$i]->{$columnName});
    
        return $records;
    }
    
    public function UpdateRecordColumnDataForReport($json)
    {
        if(strlen($json) == 0) return '';
        
        $return = '';
        foreach(json_decode($json) as $item) $return .=  $item->display .', ';        
        $return = substr($return, 0, -2);
        
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
        $params->model->groupBy($params->table_name.'.id');
        
        return $params;
    }
    
    
    
    /****    Common Function    ****/

    public function responseReportTable($data)
    {
        $data = $this->FillReportFileInfo($data);
        
        
        $fnc = 'responseTableReport';
        if(!isset($data['reportFile'])) $fnc .= 'Standart';
        else $fnc .= 'Custom';
        $fnc .= ucfirst($data['params']->report_format);

        
        $report = $this->{$fnc}($data);
        $this->SaveDownloadedReport($data);
        return $report;
    }
    
    private function SaveDownloadedReport($data)
    {
        (new ExcelStandartTableCollectionLibrary($data))->store($data['storePath'], $data['storage']);
        
        $uId = @\Auth::user()->id;
        
        $file =
        [
            'disk' => env('FILESYSTEM_DRIVER', 'uploads'),
            'file_name' => $uId.'_'.$data['tableDisplayName'].'.xlsx',
            'destination_path' => UPLOAD_PATH.date("/Y/m/d/")
        ];
        
        $now = \Carbon\Carbon::now();
        
        $data = 
        [
            'download_user_id' => $uId,
            'report_id' => $data['params']->report_id,
            'download_time' => $now,
            'report_file' => json_encode([$file]),
            'state' => TRUE,
            'created_at' => $now,
            'updated_at' => $now,
            'user_id' => ROBOT_USER_ID,
            'own_id' => ROBOT_USER_ID
        ];
        
        DB::table('downloaded_reports')->insert($data);
    }
    
    private function FillReportFileInfo($data)
    {
        global $pipe;
        
        $data['tableDisplayName'] = get_attr_from_cache('tables', 'name', $pipe['table'], 'display_name');
        $data['tableDisplayName'] .= ' '. date("d-m-Y H:i:s");
        
        $uId = @\Auth::user()->id;
        
        $data['storePath'] = UPLOAD_PATH.date("/Y/m/d/").$uId.'_'.$data['tableDisplayName'].'.'.$data['params']->report_format;
        $data['storage'] = env('FILESYSTEM_DRIVER', 'uploads');
        
        return $data;
    }
    
    public function responseTableReportCustomXlsx($data)
    {
        $file = $data['reportFile'];
        //dd(9933, $file);

        return Excel::download(Excel::import(new ExcelCustomTableCollectionLibrary($data), $file->destination_path.$file->file_name, 'uploads'), 'asd.xlsm');
    

        $file = $data['reportFile'];
        //dd($file->destination_path.$file->file_name);
        Excel::load('./uploads/2020/07/15/GecKalmaRapor.xlsm', function($reader) 
        {
            dd(99);
            // Getting all results
            $results = $reader->get();
        
            // ->all() is a wrapper for ->get() and will work the same
            $results = $reader->all();
        
        });
    }
    
    public function responseTableReportStandartXlsx($data)
    {
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.xlsx');
    }
    
    public function responseTableReportStandartCsv($data)
    {
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    public function responseTableReportStandartPdf($data)
    {
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }
}
