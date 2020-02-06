<?php

$files = 
[
    'ColorClasses',
    'ColumnTypes',
    'Validations',
    'ColumnCollectiveInfos',
    'Subscribers',
    'GuiTriggers',
    'ColumnArrayTypes',
    'ColumnSetTypes',    
    'ColumnsAndTables',
    'DataFilterTypes',
    'DataFilters',
    'Settings',
    'Departments',
    'Users',
    
    'IdDependentRecordsUpdate',
    
    'DataEntegratorDatas',
    'CustomLayerDatas',
    
    'LogLevels',
    
    'TableGroups',
    'Temp'
];

foreach($files as $file)
{
    require $file.'.php';
    echo $file." Data Insert OK\n";
}