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
            $temp = (array)$ex;
            
            DB::rollBack();
            helper('data_entegrator_log', ['danger', 'Data entegrator exception', 
            [
                $ex->getMessage(),
                $ex->getFile(),
                $ex->getLine(),
                ["exceptionObject" => json_encode($temp)],
            ]]);

            \Storage::disk('public')->put('dataEntegratorStatus/'.$tableRelationId.'.status', 'err.'.$ex->getMessage());
            
            $this->info('Hata Olustu');
        }
        
        DB::commit();

        $this->info('OK');
    }
}
