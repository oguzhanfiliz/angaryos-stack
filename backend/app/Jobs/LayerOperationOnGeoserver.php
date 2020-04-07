<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Libraries\TableGeoServerOperationsLibrary;
use App\BaseModel;

class LayerOperationOnGeoserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $type, $id;
    
    public function __construct($type, $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function handle()
    {
        if(strstr($_SERVER['argv'][0], 'phpunit')) return;
        
        $record = new BaseModel('tables');
        $record = $record->find($this->id);
        
        $params =
        [
            'type'=> $this->type,
            'table' => $record 
        ];
        
        $helper = new TableGeoServerOperationsLibrary();
        $return = $helper->TableEvent($params);
    }
}