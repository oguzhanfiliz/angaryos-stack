<?php

namespace App\Libraries;

use Storage;
use Cache;
use DB;

trait DashboardLibraryTrait
{
    public $colors = ['#868e96', '#ffaaff', '#cbac1c', '#89668e', '#aaffff', '#c1ccba', '#868012', '#ffcca1', '#cbb991', '#85628e', '#a1452f', '#c1256a', '#8abca2', '#f12341', '#c52ac1', '#8acbae', '#afacff', '#cffffa', '#f1aaa1', '#c5acc1', '#8ac32e', '#afa33f', '#cfacfa'];
    
    public function RefreshableNumberJobCount($param2)
    {
        return 
        [
            'display_name' => 'Kuyruktaki İş Sayısı',
            'number' => DB::table('jobs')->count('*')
        ];
    }
    
    private function ComboBoxListTestData()
    {
        $data = 
        [
            ['html' => '<td class="w70 comboboxlist-td"><img class="rounded-circle file-preview" src="/assets/img/404.png" alt=""></td><td class="comboboxlist-td">Ana Yönetici</td><td class="comboboxlist-td">57 dk/gün</td>'],
            ['html' => '<td class="w70 comboboxlist-td"><img class="rounded-circle file-preview" src="/assets/img/404.png" alt=""></td><td class="comboboxlist-td">Serbest Kullanıcı</td><td class="comboboxlist-td">45 dk/gün</td>']
        ];

        $comboBoxData = 
        [
            [
                'source' => '',
                'display' => 'Seçiniz'
            ],
            [
                'source' => 'secenek1',
                'display' => '1. Seçenek'
            ],
            [
                'source' => 'secenek2',
                'display' => '2. Seçenek'
            ]
        ];

        return 
        [
            'title' => 'En Aktif Üyeler',
            'combobox' => $comboBoxData,
            'data' => $data
        ];
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