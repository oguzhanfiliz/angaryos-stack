<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Storage;
use File;

class TempClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear temp files.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    function is_dir_empty($dir) 
    {
        if (!is_readable($dir)) return NULL; 
        
        $handle = opendir($dir);
        
        while (false !== ($entry = readdir($handle))) 
        {
            if ($entry != "." && $entry != "..") 
            {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {  
        $fileBase = base_path().'/public/';
        $tempBase = './temps/';
        
        $fl = Storage::allFiles($tempBase);        
        $dl = Storage::allDirectories($tempBase);
        
        foreach($fl as $f) 
            if(((int)(time() - File::lastModified($fileBase.$f)))/(60*15) > 1)//60 saniye * 15 dk (yani 15 dkdan daha eski ise)
                Storage::delete($fileBase.$f);
                
        for($i = count($dl) - 1; $i >= 0; $i--)
            if($this->is_dir_empty($fileBase.$dl[$i]))
                Storage::deleteDirectory($fileBase.$dl[$i]);
            
        
        @mkdir(base_path().'/public/temps', 777);
        
        exec ('chmod 777 -R '.base_path().'/public/temps');
        
        $this->info('Temizlendi.');
    }
}