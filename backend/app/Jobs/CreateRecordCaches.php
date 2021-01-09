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

    public $record, $tableName;
    public $timeout = 120;
    
    public function __construct($record, $tableName)
    {
        $this->record = $record;
        $this->tableName = $tableName;
    }

    public function handle()
    {
        if($this->tableName == NULL) return TRUE;
        
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

    public function error($exception)
    {
        \Log::alert('CreateRecordCaches:'.$exception->getMessage().':'.json_encode([(array)$this, debug_backtrace()]));
    }
}
