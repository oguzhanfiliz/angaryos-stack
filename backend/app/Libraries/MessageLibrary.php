<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Mail;
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
    
    public static function curl($url, $method = 'GET', $data = NULL, $header = NULL)
    {
        send_log('info', 'Curl request starting', [$url]);
        
        $ch = curl_init(); 
        curl_setopt($ch,CURLOPT_URL, $url);
        
        if($header != NULL)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        
        if($method == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            
            if($data != NULL)
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $time = microtime(TRUE);
        $response = curl_exec($ch);
        $time = microtime(TRUE) - $time;
        
        $error = FALSE;
        if($response === FALSE) $error = [curl_errno($ch), curl_error($ch)];
        
        curl_close($ch);
        
        send_log('info', 'Curl request end', [$url, $response, $error]);
        
        return ['response' => $response, 'error' => $error];
    }
    
    public static function sendSms($tel, $message) 
    {
        send_log('info', 'Send sms request', [$tel, $message]);
        
        $url = str_replace('$TEL', $tel, SMS_URL);
        $url = str_replace('$MESSAGE', $message, $url);
        
        $data = self::curl($url);
        
        send_log('info', 'Send sms end', [$data]);
        
        return $data['error'] != FALSE;
    }
    
    /**
    *
    * Send e-mail
    *
    * @param string $subject Mail subject
    * @param string $mail Mail content. It be html if you want
    * @param array $emailsWithNames Ex: [['email' => 'emailone.to', 'name' => 'Name One'], ['email' => 'emailtwo.to', 'name' => 'Name Two']]
    * @param array $attachments Ex: ['/var/www/public/uploads/2020/01/01/auths.png']
     * 
    * @return boolean
    *
    */
    public static function sendMail($subject, $mail, $emailsWithNames, $attachments = [])
    {
        Mail::send("email.base", ["html" => $mail], function ($message) use($subject, $emailsWithNames, $attachments)
        {
            foreach($emailsWithNames as $emailsWithName)
                $message->to($emailsWithName['email'], $emailsWithName['name'])->subject($subject);
            
            foreach($attachments as $attachment)
                $message->attach($attachment);
        });
        
        return Mail::failures();
    }
    
    public function fireBaseCloudMessaging($title, $text, $to = FB_BASE_TOPIC)
    {
        send_log('info', 'Send FCM request', [$to, $title, $text]);
        
        $o = (object)[];
        $o->notification = (object)[];
        $o->notification->title = $title;
        $o->notification->body = $text;
        $o->notification->sound = 'default';
        $o->to = $to;
        
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $header =
        [
            'Content-Type: application/json',
            'Authorization: key='.FB_AUTH_KEY
        ];
        
        $data = self::curl($url, 'POST', json_encode($o), $header);
        
        send_log('info', 'Send FCM end', [$data]);
        
        return $data['error'] != FALSE;
    }
}