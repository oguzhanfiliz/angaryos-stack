<?php

namespace App\Libraries;


use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ExcelStandartTableCollectionLibrary implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    use Exportable;
    
    private $collectiveInfoNames = 
    [
        'sum' => 'Toplam',
        'count' => 'Adet',
        'min' => 'En az',
        'max' => 'En çok'
    ];
    private $data;
    
    public function __construct($data) 
    {
        $this->data = $data; 
        $this->sortColumns();
    }
    
    private function sortColumns()
    {
        if($this->data['overrideColumnSort']) return;
        
        if(!@$this->data['params']->columnNames) return;
        
        $columns = [];    
        foreach($this->data['params']->columnNames as $columnName)
            foreach($this->data['columns'] as $column)
                if($columnName == $column->name)
                    $columns[$column->name] = $column;
                
        foreach($this->data['columns'] as $column)
            if(!in_array($column->name, $this->data['params']->columnNames))
                $columns[$column->name] = $column;

        $this->data['columns'] = $columns;
    }

    public function collection()
    {
        $collect = [];
        
        foreach($this->data['records'] as $record)
        {
            $temp = [];
            foreach($this->data['columns'] as $columnName => $column)
                $temp[$columnName] = $record->{$columnName};
                
            array_push($collect, $temp);
        }
        
        $temp = [];
        foreach($this->data['columns'] as $columnName => $column)
        {
            if(!isset($this->data['collectiveInfos'][$columnName]))
            {
                $temp[$columnName] = '';
                continue;
            }
            
            $type = $this->data['collectiveInfos'][$columnName]['type'];
            $temp[$columnName] = $this->collectiveInfoNames[$type];
            $temp[$columnName] .= ': ' . $this->data['collectiveInfos'][$columnName]['data'];
        }
        array_push($collect, $temp);
        
        return collect($collect);
    }
    
    public function map($invoice): array
    {
        $map = [];
        foreach($this->data['columns'] as $column)
        {
            $data = $invoice[$column->name];
            switch ($column->gui_type_name)
            {
                //case 'datetime':
                //    array_push($map, Date::dateTimeToExcel($invoice[$column->name]));
                //    break;
                default:
                    $data = $data;
            }
            
            array_push($map, $data);
        }
        
        return $map;
    }
    
    public function columnFormats(): array
    {
        $format = [];
        $col = 'A';
        foreach($this->data['columns'] as $column)
        {
            switch ($column->gui_type_name)
            {
                case 'datetime':
                    $format[$col++] = NumberFormat::FORMAT_DATE_DDMMYYYY;
                    break;
                default:
                    $col++;
            }
        }
        
        return $format;
    }

    public function headings(): array
    {
        $columns = [];
        foreach($this->data['columns'] as $column)
            array_push ($columns, $column->display_name);
                
        return $columns;
    }
    
    public function registerEvents(): array
    {
        $start = 'A';
        $end = 'A';
        foreach($this->data['columns'] as $col)
            $end++;
        return 
        [
            AfterSheet::class => function(AfterSheet $event) use ($start, $end)
            {
                $event->sheet->styleCells(
                    $start.'1:'.$end.'1',
                    [
                        'font' => array(
                            'name'      =>  'Calibri',
                            'size'      =>  15,
                            'bold'      =>  true,
                            'color' => ['argb' => '9d321b'],
                        )
                    ]
                );
            },
        ];
    }
}