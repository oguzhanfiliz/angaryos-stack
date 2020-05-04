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
                    Artisan::call('data:entegrator', 
                    [
                        'tableRelationId' => $dataEntegrator->id,
                    ]);
                })->cron($dataEntegrator->cron);
    }
    
    private function missionsSchedule(Schedule $schedule) 
    {
        $missions = DB::table('missions')->where('state', TRUE)->get();        
        foreach($missions as $mission)
            if(strlen($mission->cron) > 0)
                $schedule->call(function () use($mission) 
                {
                    eval(helper('clear_php_code', $mission->php_code));  
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
