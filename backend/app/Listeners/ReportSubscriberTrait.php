<?php

namespace App\Listeners;

use Maatwebsite\Excel\Facades\Excel;

use \App\Libraries\ExcelStandartTableCollectionLibrary;

use DB;

trait ReportSubscriberTrait 
{
    /****        ****/
    
    private $collectiveInfoNames = 
    [
        'sum' => 'Toplam',
        'count' => 'Adet',
        'min' => 'En az',
        'max' => 'En çok'
    ];
    
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
        
        $type = 'standart';//mapped
        $startRow = 1;
        $startCol = 'A';
        $activeSheet = 0;
        
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
            'activeSheet' => $activeSheet
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
            $guiTypeName = get_attr_from_cache('column_gui_types', 'id', $column->column_gui_type_id, 'name');
             
            if(strstr($dbTypeName, 'json')) $jsonColumns[$column->name] = [$dbTypeName, $guiTypeName];
        }
        
        foreach($records as $i => $record)
            foreach($jsonColumns as $columnName => $types)
                $records[$i]->{$columnName} = $this->UpdateRecordColumnDataForReport($records[$i]->{$columnName}, $types);
    
        return $records;
    }
    
    public function UpdateRecordColumnDataForReport($json, $types)
    {
        if(strlen($json) == 0) return '';
        if($json == '[]') return '';
        
        $return = '';

        $json = json_decode($json);
        switch($types[1])
        {
            case 'json':
            case 'jsonb':
                foreach($json as $item) $return .=  $item->display .', ';
                $return = substr($return, 0, -2);    
                break;
            case 'files':
                foreach($json as $item) $return = helper('get_file_url', $item) . ', ';
                $return = substr($return, 0, -2);    
                break;
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

        $this->InsertDownloadedReportRecord($data);
        
        return $this->{$fnc}($data);
    }
    
    private function SaveStandartDownloadedReport($data)
    {
        (new ExcelStandartTableCollectionLibrary($data))->store($data['storePath'], $data['storage']);
    }
    
    private function InsertDownloadedReportRecord($data)
    {
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
        
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        $spreadsheet = $reader->load($file->destination_path.$file->file_name);
        
        $this->InjectDataInCustomTableReport($data, $file, $spreadsheet);
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        mkdir($data['storePath'].'/../', 0777, TRUE);
        $writer->save($data['storePath']);
        
        header('Content-Disposition: attachment; filename='.$data['tableDisplayName'].'.xlsx');
        return file_get_contents($data['storePath']);
    }
    
    private function GetSortedColumnsForInjectDataInCustomTableReport($data)
    {
        if(!@$data['params']->columnNames) return;
        
        $columns = [];    
        foreach($data['params']->columnNames as $columnName)
            foreach($data['columns'] as $column)
                if($columnName == $column->name)
                    $columns[$column->name] = $column;
                
        foreach($data['columns'] as $column)
            if(!in_array($column->name, $data['params']->columnNames))
                $columns[$column->name] = $column;

        return $columns;
    }
    
    private function InjectDataInCustomTableReport($data, $file, $spreadsheet)
    {
        $this->{'InjectDataInCustomTableReport'.ucfirst($data['type'])}($data, $file, $spreadsheet);
    }
    
    private function InjectDataInCustomTableReportStandart($data, $file, $spreadsheet)
    {
        $sheet = $spreadsheet->getSheet($data['activeSheet']);
        
        $c = $data['startCol'];
        $columns = $this->GetSortedColumnsForInjectDataInCustomTableReport($data);
        foreach($columns as $column)
        {
            $sheet->setCellValue($c.$data['startRow'], $column->display_name);
            
            $i = $data['startRow'] + 1;
            foreach($data['records'] as $record)
                $sheet->setCellValue($c.($i++), $record->{$column->name});
            
            if(isset($data['collectiveInfos'][$column->name]))
            {
                $info = $data['collectiveInfos'][$column->name];
                $sheet->setCellValue($c.$i, $this->collectiveInfoNames[$info['type']].': '.$info['data']);
            }
             
            $c++;
        }
    }
    
    public function responseTableReportStandartXlsx($data)
    {
        $this->SaveStandartDownloadedReport($data);
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.xlsx');
    }
    
    public function responseTableReportStandartCsv($data)
    {
        $this->SaveStandartDownloadedReport($data);
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    public function responseTableReportStandartPdf($data)
    {
        $this->SaveStandartDownloadedReport($data);
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }
}
