<?php

use Spatie\Async\Pool;

global $pipe;
$pipe['asyncWorkPool'] = Pool::create()->concurrency(10)->timeout(10);

$settings = \Cache::rememberForever('settings', function()
{ 
    try 
    {
        foreach(@\DB::table('settings')->get() as $setting)
            $s[trim($setting->name)] = trim($setting->value);
        
        return @$s;
    } 
    catch (Exception $exc) 
    {
        return [];
    }
});

if(is_array($settings))
    foreach($settings as $key => $value)
        @define($key, $value);

/* Required Vars (If Not Exist) */
@define('DB_PROJECTION', 7932);