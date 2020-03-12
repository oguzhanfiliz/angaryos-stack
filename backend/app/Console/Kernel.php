<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use DB;

class Kernel extends ConsoleKernel
{
    protected $commands = [ ];

    protected function schedule(Schedule $schedule)
    {
        //Data entegratör görevlerini de ekle
        
        
        $missions = DB::table('missions')->where('state', TRUE)->get();
        
        foreach($missions as $mission)
            if(strlen($mission->cron) > 0)
                $schedule->call(function () use($mission) 
                {
                    eval(helper('clear_php_code', $mission->php_code));  
                })->cron($mission->cron);
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
