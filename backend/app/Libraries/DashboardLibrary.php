<?php

namespace App\Libraries;

use Storage;
use Cache;
use DB;

class DashboardLibrary
{
    public function RecordCount($param1, $param2)
    {
        $sumAllTablesCounts = Cache::remember('sumAllTablesCounts', 60 * 60 * 24, function()
        {
            $except = ['migrations', 'password_resets', 'sessions', 'jobs', 'failed_jobs'];

            $sum = 0;
            
            $tableNames = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            foreach($tableNames as $tableName)
                if(!in_array($tableName, $except))
                    if(!strstr($tableName, '_archive'))
                        $sum += DB::table($tableName)->count();
                
            return $sum;
        });
        
        $count = DB::table($param1)->count();
        
        return 
        [
            'table_display_name' => get_attr_from_cache('tables', 'name', $param1, 'display_name'),
            'count' => $count,
            'all' => $sumAllTablesCounts
        ];
    }
    
    public function RefreshableNumber($param1, $param2)
    {
        return $this->{'RefreshableNumber'.$param1}($param2);
    }
    
    public function RefreshableNumberJobCount($param2)
    {
        return 
        [
            'display_name' => 'Kuyruktaki İş Sayısı',
            'number' => DB::table('jobs')->count('*')
        ];
    }
    
    public function DataEntegratorStatus($param1, $param2)
    {
        $relation = get_model_from_cache('data_source_tbl_relations', 'id', $param1);
        
        try
        {
            $disk = env('FILESYSTEM_DRIVER', 'uploads');
            $message = Storage::disk($disk)->get('dataEntegratorStatus/'.$param1.'.status');
            
            global $pipe;
            $pipe['table'] = 'data_source_tbl_relations';
            
            return 
            [
                'message' => $message,
                'source' => $relation->getRelationData('data_source_rmt_table_id')->display,
                'table' => $relation->getRelationData('table_id')->display_name,
                'direction' => $relation->getRelationData('data_source_direction_id')->name
            ];
        } 
        catch (\Exception $ex) 
        {
            return
            [
                'message' => 'no.data',
                'source' => @$relation->getRelationData('data_source_rmt_table_id')->display,
                'table' => @$relation->getRelationData('table_id')->display_name,
                'direction' => @$relation->getRelationData('data_source_direction_id')->name
            ];
        }
    }
    
    public function GraphicXY($param1, $param2)
    {
        if($param1 == 'Test' && $param2 == '0') return $this->GraphicXYTestData();
    }
    
    private function GraphicXYTestData()
    {
        return 
        [
            "title" => "Aylara göre kıyaslama",
            "data" => 
            [
                "columns" =>
                [
                    ['data1', 21, 8, 32, 18, 19, 17, 23, 12, 25, 37, 36, 35],
                    ['data2', 7, 11, 5, 7, 9, 16, 15, 23, 14, 55, 54, 53],
                    ['data3', 13, 1, 9, 15, 9, 31, 8, 27, 42, 18, 16, 100],
                ],
                "type" => 'area-spline',                
                "colors" =>
                [
                    'data1' => "#868e96",
                    'data2' => "#ffaaff",
                    'data3' => "#cbac1c",
                ],
                "names" => [
                    // name of each serie
                    'data1' => 'Data1',
                    'data2' => 'Data2',
                    'data3' => 'Data3',
                ]
            ],
            "axis" =>
            [
                "x" =>
                [
                    "type" => 'category',
                    "categories" => ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara']
                ],
            ],
            "legend" => 
            [
                "show" => true, 
            ],
            "padding" =>
            [
                "bottom" => 0,
                "top" => 0
            ],
        ];
    }
    
    public function GraphicPie($param1, $param2)
    {
        if($param1 == 'Test' && $param2 == '0') return $this->GraphicPieTestData();
    }
    
    private function GraphicPieTestData()
    {
        return 
        [
            "title" => "Aylara göre kullanım",
            "data" => 
            [
                "columns" =>
                [
                    ['data1', 21],
                    ['data2', 7],
                    ['data3', 13],
                ],
                "type" => 'pie',                
                "colors" =>
                [
                    'data1' => "#868e96",
                    'data2' => "#ffaaff",
                    'data3' => "#cbac1c",
                ],
                "names" => [
                    // name of each serie
                    'data1' => 'Data1',
                    'data2' => 'Data2',
                    'data3' => 'Data3',
                ]
            ],
            "axis" => [ ],
            "legend" => 
            [
                "show" => true, 
            ],
            "padding" =>
            [
                "bottom" => 0,
                "top" => 0
            ],
        ];
    }
}