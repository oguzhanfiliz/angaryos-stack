<?php

namespace App\Libraries;

use Cache;
use DB;

class DashboardLibrary
{
    public function RecordCount($param1, $param2)
    {
        $sumAllTablesCounts = Cache::remember('sumAllTablesCounts', 60 * 60 * 24, function()
        {
            $except = ['migrations', 'password_resets', 'sessions', 'jobs', 'failed_jobs'];

            $sum = 0;
            
            $tableNames = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            foreach($tableNames as $tableName)
                if(!in_array($tableName, $except))
                    if(!strstr($tableName, '_archive'))
                        $sum += DB::table($tableName)->count();
                
            return $sum;
        });
        
        $count = DB::table($param1)->count();
        
        return 
        [
            'table_display_name' => get_attr_from_cache('tables', 'name', $param1, 'display_name'),
            'count' => $count,
            'all' => $sumAllTablesCounts
        ];
    }
    
    public function RefreshableNumber($param1, $param2)
    {
        return $this->{'RefreshableNumber'.$param1}($param2);
    }
    
    public function RefreshableNumberJobCount($param2)
    {
        return 
        [
            'display_name' => 'Kuyruktaki İş Sayısı',
            'number' => DB::table('jobs')->count('*')
        ];
    }
}