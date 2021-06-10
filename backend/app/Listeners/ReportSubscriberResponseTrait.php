<?php

namespace App\Listeners;

use Maatwebsite\Excel\Facades\Excel;

use \App\Libraries\ExcelStandartTableCollectionLibrary;

use DB;

use Storage;

trait ReportSubscriberResponseTrait 
{
    private $collectiveInfoNames = 
    [
        'sum' => 'Toplam',
        'count' => 'Adet',
        'min' => 'En az',
        'max' => 'En çok'
    ];



    /****    Record Common Function    ****/

    public function responseReportRecord($data)
    {
        $data = $this->FillReportFileInfo($data);
        
        $fnc = 'responseRecordReport';

        if(!isset($data['reportFile'])) $fnc .= 'StandartFile';
        else $fnc .= 'CustomFile';

        $fnc .= ucfirst($data['type']).'Data';
        $fnc .= ucfirst($data['params']->report_format);

        $this->InsertDownloadedReportRecord($data);
        
        return $this->{$fnc}($data);
    }

    private function responseRecordReportCustomFileGridDataXls($data)
    {
        return $this->responseRecordReportCustomFileStandartDataXlsx($data, 'Xls');
    }

    private function responseRecordReportCustomFileGridDataXlsx($data)
    {
        return $this->responseRecordReportCustomFileStandartDataXlsx($data, 'Xlsx');
    }

    public function responseRecordReportCustomFileStandartDataXlsx($data, $customType = 'Xlsx')
    {
        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        $file = $data['reportFile'];
        $tempPath = '/var/www/public/temps/';
        $tempFile = $file->destination_path.$file->file_name;

        $sourcePath = $file->destination_path.$file->file_name;
        if(!Storage::disk('temps')->exists($tempFile) && !Storage::disk('temps')->put($tempFile, Storage::disk($disk)->get($sourcePath))) 
            custom_abort('file.write.error:'.$file->destination_path.$file->file_name.'->'.$tempFile);
        
        $tempFile = $tempPath.$tempFile;

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($customType);
        $spreadsheet = $reader->load($tempFile);
        
        $this->InjectDataInCustomRecordReport($data, $file, $spreadsheet);
        
        @mkdir($tempPath.$data['storePath'].'/../', 0777, TRUE);
            
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $customType);
        $writer->save($tempPath.$data['storePath']);

        if(!Storage::disk($disk)->put($data['storePath'], Storage::disk('temps')->get($data['storePath'])))
            custom_abort('file.ftp.write.error:'.$data['storePath'].'->'.$data['storePath']);

        $conn_id = ftp_connect(env('FILE_HOST', 'ftp.url'));
        $login_result = ftp_login($conn_id, env('FILE_USER', 'user'), env('FILE_PASSWORD', 'password'));

        $fullPath = '';
        foreach(explode('/', $data['storePath']) as $path)
        {
            $fullPath .= $path;
            ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$fullPath);
            $fullPath .= '/';
        }
            
        header("Location: ".env('APP_URL').'uploads/'.$data['storePath']);
    }

    private function InjectDataInCustomRecordReport($data, $file, $spreadsheet)
    {
        $functionName = 'InjectDataInCustomRecordReport';
        $functionName .= ucfirst($data['type']);
        
        $this->{$functionName}($data, $file, $spreadsheet);
    }

    private function InjectDataInCustomRecordReportGrid($data, $file, $spreadsheet)
    {
        $this->InjectDataInCustomTableReportGrid($data, $file, $spreadsheet);
    }


    /****    Table Common Functions   ****/

    public function responseReportTable($data)
    {
        $data = $this->FillReportFileInfo($data);
        
        $fnc = 'responseTableReport';

        if(!isset($data['reportFile'])) $fnc .= 'StandartFile';
        else $fnc .= 'CustomFile';

        $fnc .= ucfirst($data['type']).'Data';
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
        $temp = explode('/', $data["storePath"]);
        
        $file =
        [
            'disk' => env('FILESYSTEM_DRIVER', 'uploads'),
            'file_name' => last($temp),
            'destination_path' => str_replace(last($temp), "", $data["storePath"])
        ];
        
        $now = \Carbon\Carbon::now();
        
        $report = 
        [
            'download_user_id' => $uId,
            'report_id' => $data['params']->report_id,
            'download_time' => $now,
            'report_file' => json_encode([$file]),
            'detail' => json_encode($data),
            'state' => TRUE,
            'created_at' => $now,
            'updated_at' => $now,
            'user_id' => ROBOT_USER_ID,
            'own_id' => ROBOT_USER_ID
        ];
        
        DB::table('downloaded_reports')->insert($report);
    }
    
    private function FillReportFileInfo($data)
    {
        global $pipe;
        
        if($data['report'])
            $data['tableDisplayName'] = $data['report']->name;
        else
            $data['tableDisplayName'] = get_attr_from_cache('tables', 'name', $pipe['table'], 'display_name');
        
        $data['tableDisplayName'] .= ' '. date("d-m-Y H:i:s");
        
        $uId = @\Auth::user()->id;
        
        $data['storePath'] = date("Y/m/d/").$uId.'_'.helper('seo', $data['tableDisplayName']).'.'.$data['params']->report_format;
        $data['storage'] = env('FILESYSTEM_DRIVER', 'uploads');

        if(isset($data['reportFile']))
        {
            $ext = explode('.', $data['reportFile']->file_name);
            $ext = last($ext);

            $temp = explode('.', $data['storePath']);
            $tempExt = last($temp);

            if($tempExt != $ext)
            {
                $temp[count($temp)-1] = $ext;
                $data['storePath'] = implode('.', $temp);

                $data['params']->report_format = $ext;
            }
        }
        
        return $data;
    }

    private function responseTableReportCustomFileGridDataXls($data)
    {
        return $this->responseTableReportCustomFileStandartDataXlsx($data, 'Xls');
    }
    
    private function responseTableReportCustomFileGridDataXlsx($data)
    {
        return $this->responseTableReportCustomFileStandartDataXlsx($data);
    }

    public function responseTableReportCustomFileStandartDataXlsx($data, $customType = 'Xlsx')
    {
        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        $file = $data['reportFile'];
        $tempPath = '/var/www/public/temps/';
        $tempFile = $file->destination_path.$file->file_name;

        $sourcePath = $file->destination_path.$file->file_name;
        if(!Storage::disk('temps')->exists($tempFile) && !Storage::disk('temps')->put($tempFile, Storage::disk($disk)->get($sourcePath))) 
            custom_abort('file.write.error:'.$file->destination_path.$file->file_name.'->'.$tempFile);
        
        $tempFile = $tempPath.$tempFile;

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($customType);
        $spreadsheet = $reader->load($tempFile);
        
        $this->InjectDataInCustomTableReport($data, $file, $spreadsheet);
        
        @mkdir($tempPath.$data['storePath'].'/../', 0777, TRUE);
            
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, $customType);
        $writer->save($tempPath.$data['storePath']);

        if(!Storage::disk($disk)->put($data['storePath'], Storage::disk('temps')->get($data['storePath'])))
            custom_abort('file.ftp.write.error:'.$data['storePath'].'->'.$data['storePath']);

        $conn_id = ftp_connect(env('FILE_HOST', 'ftp.url'));
        $login_result = ftp_login($conn_id, env('FILE_USER', 'user'), env('FILE_PASSWORD', 'password'));

        $fullPath = '';
        foreach(explode('/', $data['storePath']) as $path)
        {
            $fullPath .= $path;
            @ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$fullPath);
            $fullPath .= '/';
        }
            
        header("Location: ".env('APP_URL').'uploads/'.$data['storePath']);
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
        $functionName = 'InjectDataInCustomTableReport';
        $functionName .= ucfirst($data['type']);
        
        $this->{$functionName}($data, $file, $spreadsheet);
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

    private function InjectDataInCustomTableReportGrid($data, $file, $spreadsheet)
    {
        foreach($data['gridData'] as $activeSheet => $sheetData)
        {
            $sheet = $spreadsheet->getSheet($activeSheet);

            foreach($sheetData['data'] as $cellKey => $cellData)
                $sheet->setCellValue($cellKey, $cellData);

            foreach($sheetData['style'] as $cellKey => $styleData)
                $this->setCellStyle($sheet, $cellKey, $styleData);
        }
    }

    private function setCellStyle($sheet, $cellKey, $styles)
    {
        foreach($styles as $key => $style)
        {
            $key = str_replace('-', '', $key);

            $functionName = 'setCellStyle';
            $functionName .= ucfirst(helper('seo', $key));

            $this->{$functionName}($sheet, $cellKey, $style);
        }
    }

    private function setCellStyleBackgroundcolor($sheet, $cellKey, $color)
    {
        $sheet->getStyle($cellKey)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB($color);
    }

    private function setCellStyleColor($sheet, $cellKey, $color)
    {
        $sheet->getStyle($cellKey)->getFont()->getColor()->setARGB($color);
    }
    
    public function responseTableReportStandartFileStandartDataXlsx($data)
    {
        $this->SaveStandartDownloadedReport($data);
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.xlsx');
    }
    
    public function responseTableReportStandartFileStandartDataCsv($data)
    {
        $this->SaveStandartDownloadedReport($data);
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    public function responseTableReportStandartFileStandartDataPdf($data)
    {
        $this->SaveStandartDownloadedReport($data);
        return Excel::download(new ExcelStandartTableCollectionLibrary($data), $data['tableDisplayName'].'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }
}
