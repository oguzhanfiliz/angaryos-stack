<?php

namespace App\Listeners;

use Maatwebsite\Excel\Facades\Excel;

use App\Http\Requests\BaseRequest;

use App\Libraries\ChangeDataLibrary;
use App\Libraries\ColumnClassificationLibrary;
use \App\Libraries\ExcelCollectionLibrary;

use DB;
use App\BaseModel;

trait ReportSubscriberTrait 
{
    /****    List    ****/
    
    public function getDataForStandartList($model, $params) 
    {
        global $pipe;
        
        $except = ['tables', 'columns'];
        
        $params = $this->getModelForStandartList($model, $params);
        
        if(in_array($model->getTable(), $except) && $pipe['SHOW_DELETED_TABLES_AND_COLUMNS'] != '1')
            $params->model->where($model->getTable().'.name', 'not ilike', 'deleted\_%');
        
        $collectiveInfos = $model->getCollectiveInfos($params->model, $params->columns);
        
        $records = $params->model->get();
        $records = $model->updateRecordsDataForResponse($records, $params->columns);
        
        $tableInfo = $model->getTableInfo($params->table_name);
        
        $columns = $model->getFilteredColumns($params->columns);
                
        return 
        [
            'table_info' => $tableInfo,
            'records' => $records,
            'collectiveInfos' => $collectiveInfos, 
            'columns' => $columns
        ];
    }
    
    public function getModelForStandartList($model, $params)
    {
        $params->model = $model->getQuery();
        
        $params->columns = $model->getColumns($params->model, 'column_arrays', $params->column_array_id);
        
        $model->addJoinsWithColumns($params->model, $params->columns);
        $model->addSorts($params->model, $params->columns, $params->sorts);
        $model->addWheres($params->model, $params->columns, $params->filters);
        $model->addSelects($params->model, $params->columns);
        $model->addFilters($params->model, $params->table_name);
        
        $params->model->addSelect($params->table_name.'.id');
        $params->model->groupBy($params->table_name.'.id');
        
        return $params;
    }
    
    
    
    /****    Common Function    ****/
    
    public function responseListReportExcel($data)
    {
        global $pipe;
        return Excel::download(new ExcelCollectionLibrary($data), $pipe['table'].'.xlsx');
    }
    
    public function responseListReportCsv($data)
    {
        global $pipe;
        return Excel::download(new ExcelCollectionLibrary($data), $pipe['table'].'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    public function responseListReportPdf($data)
    {
        global $pipe;
        return Excel::download(new ExcelCollectionLibrary($data), $pipe['table'].'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
    }
}
