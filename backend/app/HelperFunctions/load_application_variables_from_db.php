<?php

use Spatie\Async\Pool;

global $pipe;
$pipe['asyncPool'] = Pool::create();

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