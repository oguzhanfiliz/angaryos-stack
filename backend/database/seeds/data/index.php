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
    'Missions',
    'Departments',
    'TableGroups',    
    'LogLevels',
    
    'Users',
    
    'IdDependentRecordsUpdate',
    
    'DataEntegratorDatas',
    'CustomLayerDatas',
    
    'ReportTypes',
    
    'Temp',
    
    'PublicContents',
    
    'AdditionalLinkTypes'
];

foreach($files as $file)
{
    require $file.'.php';
    echo $file." Data Insert OK\n";
}