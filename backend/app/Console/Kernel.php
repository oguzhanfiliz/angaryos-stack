<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Artisan;
use DB;

class Kernel extends ConsoleKernel
{
    protected $commands = [ ];
    
    private function dataEntegratorSchedule(Schedule $schedule) 
    {
        $dataEntegrators = DB::table('data_source_tbl_relations')->where('state', TRUE)->get();        
        foreach($dataEntegrators as $dataEntegrator)
            if(strlen($dataEntegrator->cron) > 0)
                $schedule->call(function () use($dataEntegrator) 
                {
                    try 
                    {
                        send_log('info', 'Data entegrator schedule start...', $dataEntegrator);
                        
                        Artisan::call('data:entegrator', 
                        [
                            'tableRelationId' => $dataEntegrator->id,
                        ]);

                        send_log('info', 'Data entegrator schedule complate', $dataEntegrator); 
                    } 
                    catch (\Exception $ex) 
                    {                        
                        send_log('info', 'Data entegrator schedule fail', [$dataEntegrator, $ex->getMessage(), $ex]); 
                    }   


                    
                })->cron($dataEntegrator->cron);
    }
    
    private function missionsSchedule(Schedule $schedule) 
    {
        $missions = DB::table('missions')->where('state', TRUE)->get();        
        foreach($missions as $mission)
            if(strlen($mission->cron) > 0)
                $schedule->call(function () use($mission) 
                {
                    try 
                    {
                        \DB::table('missions')->where('id', $mission->id)->update(['last_worked_at' => \Carbon\Carbon::now()]);
                        
                        send_log('info', 'Mission schedule start...', $mission);
                        eval(helper('clear_php_code', $mission->php_code)); 
                        send_log('info', 'Mission schedule complate', $mission); 
                    } 
                    catch (\Exception $ex) 
                    {                        
                        send_log('info', 'Mission schedule fail', [$mission, $ex->getMessage(), $ex]); 
                    }                    
                })->cron($mission->cron);
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('system:rutine')->daily();
        $schedule->command('temp:clear')->hourly();
        
        $this->dataEntegratorSchedule($schedule);
        $this->missionsSchedule($schedule);
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
