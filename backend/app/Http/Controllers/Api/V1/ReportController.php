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
        global $pipe;
        $pipe['table'] = helper('get_table_name_from_url');
        
        $this->fillAuthFunctions();        
    }
    
    
    
    /****    List Report Functions    ****/
        
    public function index(User $user, BaseModel $model)
    {   
        send_log('info', 'Request Default List Report');
        
        $params = $this->getValidatedParamsForList();   
        param_is_have($params, 'report_type');
        if(Gate::denies('viewAny', $params)) $this->abort();
        
        $data = Event::dispatch('standart.list.report.requested', [$model, $params])[0];
        
        send_log('info', 'Response Data For Default List Report', [$params->report_type, $data]);
        
        return Event::dispatch('standart.list.report.data.responsed', [$params->report_type, $data])[0];
    }
}
