<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

use App\Libraries\MessageLibrary;

class RabbitMQLogHandler extends AbstractProcessingHandler
{
    public function __construct($level = Logger::DEBUG)
    {
        parent::__construct($level);
    }
    
    protected function write(array $record): void
    {
        $log = $this->format($record);
        MessageLibrary::sendToRabbitMQ('logs', $log);
    }
    
    private function format($record)
    {
        if(substr($record['message'], 0, 1) == '{')
        {
            $return = json_decode($record['message']);
            $return->log_level = $record['level_name'];
        }
        else
        {
            $return = helper('get_null_object');
            $return->message = $record['message'];
            $return->log_level = $record['level_name'];
        }
        
        if(isset($return->obj))
        {
            $infos = ['user', 'waitTime', 'host', 'logRandom', 'uri', 'logTime', 'ip', 'response_data'];
            $temp = json_decode($return->obj);
            foreach($infos as $info)
                if(isset($temp->{$info}))
                {
                    $return->{$info} = $temp->{$info};
                    unset($temp->{$info});
                }
            
            $return->obj = json_encode($temp); 
        }
        
        return json_encode($return);
    }
}