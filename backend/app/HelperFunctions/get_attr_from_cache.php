<?php

if($requestData == NULL) return NULL;
if($requestData == 0 && $requestColumn == 'id') return NULL;

if($requestColumn == $responseColumn) return $requestData;

if(is_array($requestData)) $requestData = json_encode($requestData);

$cacheKey = 'tableName:'.$tableName.'|columnName:'.$requestColumn.'|columnData:'.$requestData.'|returnData:'.$responseColumn;
$value = Cache::rememberForever($cacheKey, function() use($cacheKey, $tableName, $requestColumn, $requestData, $responseColumn)
{
    $record = DB::table($tableName)->where($requestColumn, $requestData)->first();
    if($record == NULL) return NULL;
    
    \App\Jobs\CreateRecordCaches::dispatch($record, $tableName);
    
    if($responseColumn == '*') return $record;
    
    return @$record->{$responseColumn};
});

return $value; 