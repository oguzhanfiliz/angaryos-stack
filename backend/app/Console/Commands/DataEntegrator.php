<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\DataEntegratorLibrary;

class DataEntegrator extends Command
{
    protected $signature = 'data:entegrator {tableRelationId}';
    protected $description = 'Data entegrator';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $tableRelationId = $this->argument('tableRelationId');
        
        $helper = new DataEntegratorLibrary($tableRelationId);
        $helper->entegrate();
    }
}
