<?php

namespace App\Libraries;

use Maatwebsite\Excel\Facades\Excel;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Files\LocalTemporaryFile;

class ExcelCustomTableCollectionLibrary implements FromCollection, WithEvents
{
    public $data;

    public function __construct($data) 
    {
        $this->data = $data; 
    }

    public function collection()
    {
        dd(234, 'collection');
        if ($this->calledByEvent) { // flag
            return $this->myCollectionToExport;
        }

        return collect([]);
    }

    public function registerEvents(): array
    {
        return 
        [
            BeforeWriting::class => function(BeforeWriting $event) 
            {
            dd(99);
                $templateFile = new LocalTemporaryFile('./uploads/2020/07/15/GecKalmaRapor.xlsm');
                $event->writer->reopen($templateFile, Excel::XLSX);
                $event->writer->getSheetByIndex(0);

                $this->calledByEvent = true; // set the flag
                $event->writer->getSheetByIndex(0)->export($event->getConcernable()); // call the export on the first sheet
                dd($event->writer->getSheetByIndex(0));
                return $event->getWriter()->getSheetByIndex(0);
            },
        ];
    }
}