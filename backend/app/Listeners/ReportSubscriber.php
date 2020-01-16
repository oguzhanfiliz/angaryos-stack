<?php

namespace App\Listeners;

class ReportSubscriber 
{
    use ReportSubscriberTrait;
    
    public function standartListReportRequested($model, $params) 
    {
        return $this->getDataForStandartList($model, $params);
    }
    
    public function responseListReport($type, $data)
    {
        switch($type)
        {
            case 'excel':
                return $this->responseListReportExcel($data);
            case 'csv':
                return $this->responseListReportCsv($data);
            case 'pdf':
                return $this->responseListReportPdf($data);
            default:
                custom_abort('invalid.report.type.'.$type);
        }
    }
}
