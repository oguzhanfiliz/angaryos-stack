<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\DataEntegratorLibrary;

use DB;

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
        
        send_log('info', 'Data entegrator cron handle: ' . $tableRelationId);
        
        DB::beginTransaction();
        
        try 
        {            
            $helper = new DataEntegratorLibrary($tableRelationId);
            $helper->Entegrate();
        } 
        catch (\Exception $ex) 
        {
            DB::rollBack();
            helper('data_entegrator_log', ['danger', 'Data entegrator exception', 
            [
                $ex->getMessage(),
                $ex->getFile(),
                $ex->getLine(),
            ]]);
        }
        
        DB::commit();

        $this->info('OK');
    }
}
