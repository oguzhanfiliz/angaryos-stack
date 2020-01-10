<?php

namespace App\Logging;

use Monolog\Logger;

class CreateRabbitMQLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('custom');
        $logger->pushHandler(new RabbitMQLogHandler());
        
        return $logger;
    }
}