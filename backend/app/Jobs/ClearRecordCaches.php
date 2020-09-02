<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Listeners\CacheSubscriber;
use App\BaseModel;

use Cache;

class ClearRecordCaches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tableName, $data, $type;
    
    public function __construct($tableName, $data, $type)
    {
        $this->tableName = $tableName;
        $this->data = $data;
        $this->type = $type;
    }

    public function handle()
    {
        $record = new BaseModel($this->tableName);
        $record = $record->find($this->data['id']);

        if($record == NULL)
        {
            $record = new BaseModel($this->tableName);
            $record = $record->first();
            $record->id = $this->data['id'];
        }
        
        foreach($this->data as $columnName => $value);
            $record->{$columnName} = $value;

        $listener = new CacheSubscriber();
        $listener->recordChangedSuccess($this->tableName, $record, $this->type);
    }
}
