<?php
use \App\BaseModel;

if($requestData == NULL) return NULL;
if($requestData == 0 && $requestColumn == 'id') return NULL;

if($requestColumn == $responseColumn) return $requestData;

if(is_array($requestData)) $requestData = implode('|', $requestData);

if($responseColumn == '*')
{
    $cacheKey = 'tableName:'.$tableName.'|columnName:'.$requestColumn.'|columnData:'.$requestData.'|returnData:BaseModel';
    $record = Cache::rememberForever($cacheKey, function() use($tableName, $requestColumn, $requestData)
    { 
        $model = new BaseModel($tableName);
        return $model->where($requestColumn, $requestData)->first();
    });

    if($record == NULL) return NULL;

    foreach($record->toArray() as $columnName => $value)
    {
        if(is_array($value)) $value = json_encode($value);
        
        $cacheKey = 'tableName:'.$tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:BaseModel';
        Cache::forever($cacheKey, $record);
    }

    return $record;
}
else
{
    $cacheKey = 'tableName:'.$tableName.'|columnName:'.$requestColumn.'|columnData'.$requestData.'|returnData:'.$responseColumn;
    $value = Cache::rememberForever($cacheKey, function() use($tableName, $requestColumn, $requestData, $responseColumn)
    { 
        $record = DB::table($tableName)->where($requestColumn, $requestData)->first();
        if($record == NULL) return NULL;
        
        $return = @$record->{$responseColumn};
        
        foreach($record as $requestColumn => $requestData)
            foreach($record as $responseColumn => $responseData)
            {
                if($requestColumn == $responseColumn) continue;

                $cacheKey = 'tableName:'.$tableName.'|columnName:'.$requestColumn.'|columnData'.$requestData.'|returnData:'.$responseColumn;
                Cache::forever($cacheKey, $responseData);
            }
            
        return $return;
    });

    return $value; 
}