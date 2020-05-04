<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SystemRutine extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:rutine';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do rutine operations.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
        try 
        {
            $file = './storage/logs/laravel-'.date("Y-m-d").'.log';
            touch($file);
            chmod($file, 0777);
        } 
        catch (\Exception $ex) { }
    }
}