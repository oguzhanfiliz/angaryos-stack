<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Cache;

use App\BaseModel;

class CreateRecordModelCaches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $recordId, $tableName;
    
    public function __construct($recordId, $tableName)
    {
        $this->recordId = $recordId;
        $this->tableName = $tableName;
    }

    public function handle()
    {
        $model = new BaseModel($this->tableName);
        $model = $model->find($this->recordId);
        if($model == NULL) return;
        
        foreach($model->toArray() as $columnName => $value)
        {
            if(is_array($value)) $value = json_encode($value);

            $cacheKey = 'tableName:'.$this->tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:BaseModel';
            Cache::forever($cacheKey, $model);
        }
    }
}
