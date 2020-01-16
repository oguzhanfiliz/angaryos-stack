<?php
use \App\BaseModel;

if($requestData == NULL) return NULL;
if($requestData == 0 && $requestColumn == 'id') return NULL;

if(is_array($requestData)) $requestData = json_encode($requestData);

$cacheKey = 'tableName:'.$tableName.'|columnName:'.$requestColumn.'|columnData:'.$requestData.'|returnData:BaseModel';
$record = Cache::rememberForever($cacheKey, function() use($tableName, $requestColumn, $requestData)
{ 
    $model = new BaseModel($tableName);
    return $model->where($requestColumn, $requestData)->first();
});

if($record == NULL) return NULL;

App\Jobs\CreateRecordModelCaches::dispatch($record, $tableName);

return $record;