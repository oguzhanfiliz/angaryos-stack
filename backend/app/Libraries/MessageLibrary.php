<?php

namespace App\Libraries;
use Log;

class MessageLibrary 
{
    private static $rabbitMQObject = NULL;
    
    private static function getRabbitMQObject($queue)
    {
        if(self::$rabbitMQObject != NULL) return self::$rabbitMQObject;
        
        $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
                                                                    env('RABBITMQ_HOST', 'rabbitmq'), 
                                                                    env('RABBITMQ_PORT', 5672),
                                                                    env('RABBITMQ_USER', 'guest'),
                                                                    env('RABBITMQ_PASSWORD', 'guest'));
        
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, false, false, false);
        
        self::$rabbitMQObject = helper('get_null_object');
        self::$rabbitMQObject->connection = $connection;
        self::$rabbitMQObject->channel = $channel;
        
        return self::$rabbitMQObject;
    }
    
    public static function sendToRabbitMQ($queue, $message)
    {
        if(is_array($message) || is_object($message)) $message = json_encode($message);
        
        $connectionObject = self::getRabbitMQObject($queue);
        $msg = new \PhpAmqpLib\Message\AMQPMessage($message);
        
        $connectionObject->channel->basic_publish($msg, '', $queue);
    }
}