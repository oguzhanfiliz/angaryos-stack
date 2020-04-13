<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Artisan;

class DoSingleEntegrate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tableRelationId;
    
    public function __construct($tableRelationId)
    {
        $this->tableRelationId = $tableRelationId;
    }

    public function handle()
    {
        Artisan::call('data:entegrator', 
        [
            'tableRelationId' => $this->tableRelationId,
        ]);
    }
}