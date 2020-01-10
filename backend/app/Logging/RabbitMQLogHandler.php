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
        
        $user = \Auth::user();
        if($user) $return->user = $user->id.'-'.$user->name_basic.' '.$user->surname;
        
        global $pipe;
        $return->log_random = $pipe['log_random'];
        
        $return->host = @$_SERVER['HOSTNAME'];
        $return->uri = @$_SERVER['REQUEST_URI'];
        
        $return->wait_time = helper('get_wait_time');
        
        return json_encode($return);
    }
}