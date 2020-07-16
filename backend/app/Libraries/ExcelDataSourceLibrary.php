<?php

namespace App\Libraries;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\ToModel;


class ExcelDataSourceLibrary implements ToModel, WithEvents
{
    public function registerEvents(): array
    {
        return 
        [
            BeforeSheet::class => [self::class, 'beforeSheet']
        ];
    }
    
    public static function beforeSheet(BeforeSheet $event) 
    {
        $page = $event->getSheet()->getTitle();
        
        global $pipe;
        $pipe['excelData'][$page] = [];
    }
    
    public function model(array $row)
    {
        global $pipe;
        
        $pages = array_keys($pipe['excelData']);
        $page = last($pages);
        
        array_push($pipe['excelData'][$page], $row);
        
        return NULL;
    }
}