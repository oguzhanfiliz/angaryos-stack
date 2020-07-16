<?php

namespace App\Libraries;

use Maatwebsite\Excel\Facades\Excel;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Files\LocalTemporaryFile;

class ExcelCustomTableCollectionLibrary implements FromCollection, WithEvents
{
    use Exportable, RegistersEventListeners;
    
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
        $styleTitulos = [
            'font' => [
                'bold' => true,
                'size' => 12
            ]
        ];
        return [
            BeforeExport::class => function(BeforeExport $event) 
            {
                dd(99);
                $event->writer->getProperties()->setCreator('Sistema de alquileres');
            },
            AfterSheet::class => function(AfterSheet $event) use ($styleTitulos)
            {
                return;
                
                
                
                //$cellRange = 'A1:W1'; // All headers
                //$event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                
                
                /*tatic function beforeSheet(BeforeSheet $event){
        $event->sheet->appendRows(array(
            array('test1', 'test2'),
            array('test3', 'test4'),
            //....
        ), $event);*/
                dd($event);
                $event->sheet->getStyle("A1:G1")->applyFromArray($styleTitulos);
                $event->sheet->setCellValue('A'. ($event->sheet->getHighestRow()+1),"Total");
                foreach ($this->filas as $index => $fila){
                    $fila++;
                    $event->sheet->insertNewRowBefore($fila, 1);
                    $event->sheet->getStyle("A{$fila}:G{$fila}")->applyFromArray($styleTitulos)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FFFF0000');
                    $event->sheet->setCellValue("A{$fila}","Subtotal Propiedad");
                    $event->sheet->setCellValue("G{$fila}", "=SUM(G".($fila - $this->limites[$index]).":G".($fila - 1).")");
                }
                $event->sheet->getDelegate()->mergeCells("A{$event->sheet->getHighestRow()}:F{$event->sheet->getHighestRow()}");
                $event->sheet->setCellValue('G'. ($event->sheet->getHighestRow()), $this->total);
            }
        ];
    }

    /*public function registerEvents(): array
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
    }*/
}