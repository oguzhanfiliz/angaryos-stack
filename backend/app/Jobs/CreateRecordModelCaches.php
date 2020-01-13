<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Cache;

class CreateRecordModelCaches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $record, $tableName;
    
    public function __construct($record, $tableName)
    {
        $this->record = $record;
        $this->tableName = $tableName;
    }

    public function handle()
    {
        foreach($this->record->toArray() as $columnName => $value)
        {
            if(is_array($value)) $value = json_encode($value);

            $cacheKey = 'tableName:'.$this->tableName.'|columnName:'.$columnName.'|columnData:'.$value.'|returnData:BaseModel';
            Cache::forever($cacheKey, $record);
        }
    }
}
