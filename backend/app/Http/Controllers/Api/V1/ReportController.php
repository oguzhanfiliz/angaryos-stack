<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Phaza\LaravelPostgis\Geometries\Point;

use App\Libraries\ColumnClassificationLibrary;

use App\User;
use App\BaseModel;

use Event;
use Auth;
use Gate;
use DB;

class ReportController extends Controller
{
    use TableTrait;
    
    public function __construct()
    {
        //\Cache::flush();
        
        global $pipe;
        $pipe['table'] = helper('get_table_name_from_url');
        
        $this->fillAuthFunctions();   
        
        ini_set("memory_limit","-1");
    }
    
    
    
    /****    List Report Functions    ****/
        
    public function index(User $user, BaseModel $model)
    {   
        send_log('info', 'Request Default List Report');
        
        $params = $this->getValidatedParamsForReport();   
        
        if(Gate::denies('viewAny', $params)) $this->abort();
        
        $data = Event::dispatch('report.requested', [$model, $params])[0];
        
        $data['params'] = $params;
        send_log('info', 'Response Data For Default List Report', [$data]);
        
        return Event::dispatch('report.data.responsed', [$data])[0];
    }

    private function getValidatedParamsForReport()
    {
        $params = $this->getValidatedParamsForList();   
        param_is_have($params, 'report_format');
        param_is_have($params, 'report_id');
        param_is_have($params, 'record_id');

        if($params->report_id == 0) return $params;
        
        $report = get_attr_from_cache('reports', 'id', $params->report_id, '*');
        $reportTypeName = get_attr_from_cache('report_types', 'id', $report->report_type_id, 'name');
        
        if($reportTypeName == 'record' && $params->record_id == 0) custom_abort('record.is.not.null.for.record.report');

        return $params;
    }
}
