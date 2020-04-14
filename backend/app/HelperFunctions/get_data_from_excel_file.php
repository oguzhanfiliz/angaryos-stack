<?php

use App\Libraries\ExcelDataSourceLibrary;
use Maatwebsite\Excel\Facades\Excel;

global $pipe;
$pipe['excelData'] = [];

Excel::import(new ExcelDataSourceLibrary, $params);

$data = [];
$tempData = $pipe['excelData'];
unset($pipe['excelData']);


foreach($tempData as $pageName => $pageData)
{
    if($pageData[0][0] == NULL) continue;
    
    $data[$pageName] = 
    [
        'columns' => NULL,
        'data' => []
    ];
    
    foreach($pageData as $i => $row)
    {
        if($i == 0) 
            $data[$pageName]['columns'] = $row;
        else
        {
            $temp = [];
            foreach($row as $i => $colData)
                $temp[$data[$pageName]['columns'][$i]] = $colData;
            
            array_push($data[$pageName]['data'], $temp);
        }
            
    }
}
        
return $data;