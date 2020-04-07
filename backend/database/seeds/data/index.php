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
    'TableGroups',    
    'LogLevels',
    
    'Users',
    
    'IdDependentRecordsUpdate',
    
    'DataEntegratorDatas',
    'CustomLayerDatas',
    
    'Temp'
];

foreach($files as $file)
{
    require $file.'.php';
    echo $file." Data Insert OK\n";
}