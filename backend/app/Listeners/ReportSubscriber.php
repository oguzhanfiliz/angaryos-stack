<?php

namespace App\Listeners;

class ReportSubscriber 
{
    use ReportSubscriberTrait;
    
    public function reportRequested($model, $params) 
    {
        return $this->getDataForReport($model, $params);
    }
    
    public function responseReport($data)
    {
        $fnc = 'responseReport';

        if($data['params']->record_id > 0) $fnc .= 'Record';
        else $fnc .= 'Table';

        if($data['params']->report_id > 0)
        {
            $report = get_attr_from_cache('reports', 'id', $data['params']->report_id, '*');
            if(strlen($report->report_file) > 0 && $report->report_file != '[]') 
                $data['reportFile'] = json_decode($report->report_file)[0]; 
        }
        
        return $this->{$fnc}($data);
    }
}
