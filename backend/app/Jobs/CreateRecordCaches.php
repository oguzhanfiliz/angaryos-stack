<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Cache;

class CreateRecordCaches implements ShouldQueue
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
        foreach($this->record as $requestColumnTemp => $requestDataTemp)
        {
            $cacheKey = 'tableName:'.$this->tableName.'|columnName:'.$requestColumnTemp.'|columnData:'.$requestDataTemp.'|returnData:*';
            Cache::forever($cacheKey, $this->record);

            foreach($this->record as $responseColumnTemp => $responseDataTemp)
            {
                if($requestColumnTemp == $responseColumnTemp) continue;

                $cacheKey = 'tableName:'.$this->tableName.'|columnName:'.$requestColumnTemp.'|columnData'.$requestDataTemp.'|returnData:'.$responseColumnTemp;
                Cache::forever($cacheKey, $responseDataTemp);
            }
        }
    }
}
