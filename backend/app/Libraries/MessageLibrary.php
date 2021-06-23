<?php

namespace App\Libraries;
use phpseclib\Net\SSH2;

use Illuminate\Support\Facades\Mail;
use Log;

class MessageLibrary 
{
    private static $rabbitMQObject = NULL;
    public static $sshConnectionObjects = [];

    public static $lastSshResponse = NULL;
    public static $lastSmsResponse = NULL;

    public static function getSshConnectionObject($host, $user, $password, $port = NULL)
    {
        if(strlen($port) == 0) $port = 22;
        
        $key = $host.'|'.$port.'|'.$user.'|'.$password;
        if(isset(self::$sshConnectionObjects[$key])) return self::$sshConnectionObjects[$key];

        $connection = new SSH2($host, $port);
        $control = $connection->login($user, $password);
        
        if(!$control) return FALSE;

        self::$sshConnectionObjects[$key] = $connection;

        return $connection;
    }

    private static function runSshCommandAsSudo($connection, $command, $config)
    {
        //NET_SSH2_READ_SIMPLE 1
        
        $connection->setTimeout(1);
        if(!$config['no_wait_for_sudo']) $connection->read('/.*@.*[$|#]/', /*NET_SSH2_READ_REGEX*/2);
        else  $connection->read();

        $connection->write($command."\n");

        if(!$config['no_wait_for_sudo'])
        {
            $output = $connection->read('/.*@.*[$|#]|.*[pP]assword.*/', /*NET_SSH2_READ_REGEX*/2);
            
            if (preg_match('/.*[pP]assword.*/', $output)) 
            {
                $connection->write($config['password']."\n");
                $output = $connection->read('/.*@.*[$|#]/', /*NET_SSH2_READ_REGEX*/2);
            }
            else if(strstr($output, $config['user_name'].'@')) 
            {
                $output = str_replace([ " \r ", " \r", "\r ", "\r"], '', $output);
                $output = str_replace($command, '', $output);
            }
            else throw new \Exception('command.not.run.as.sudo:'.json_encode($output));
        }
        else 
        {
            $output = $connection->read('/.*@.*[$|#]/', /*NET_SSH2_READ_REGEX*/2);
            $output = str_replace([ " \r ", " \r", "\r ", "\r"], '', $output);
            $output = str_replace($command, '', $output);
        }
        
        return $output;
    }

    private static function clearSshOutput($output, $config)
    {
        $output = str_replace([ " \r ", " \r", "\r ", "\r"], '', $output);
        $output = trim($output, ' ');
        $arr = [];
        foreach(explode("\n", $output) as $line)
        {
            $line = trim($line, "\r");
            if(strlen($line) == 0) continue;
            if(strstr($line, $config['user_name'].'@')) continue;
            array_push($arr, $line);
        }

        $output = $arr;

        if(strlen(last($output)) == 0) unset($output[count($output)-1]);

        if(count($output) == 0) return '';
        else if(count($output) == 1) return $output[0]; 
        else return $output;
    }

    public static function sendCommandWithSsh($config, $command, $clearAndParseOutput = TRUE, $timeOut = 5)
    {
        if(!isset($config['port'])) $config['port'] = 22;

        $connection = self::getSshConnectionObject($config['host'], $config['user_name'], $config['password'], $config['port']);
        if(!$connection) throw new \Exception('ssh.server.connection.error');
        
        $connection->setTimeout($timeOut);

        if(substr(strtolower(trim($command)), 0, 5) == 'sudo ')
            $output = self::runSshCommandAsSudo($connection, $command, $config);
        else 
        {
            if(!isset($config['call_back_function']))
                $output = $connection->exec($command);
            else
                $output = $connection->exec($command, $config['call_back_function']);
        }

        self::$lastSshResponse = $output;
        
        if($clearAndParseOutput) $output = self::clearSshOutput($output, $config);

        return $output;
    }
    
    private static function getRabbitMQObject($queue)
    {
        try 
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
        catch (\Exception $ex) 
        {
            $json = json_encode(
            [
                'mesaage' => $ex->getMessage(),
                'ex' => (array)$ex                
            ]);
            
            //if(!strstr($json['message'], 'Unsupported image type.'))
                \Log::alert('Rabitt nesne oluşturulurken hata oluştu... (json:'.$json.')');
        }
    }
    
    public static function sendToRabbitMQ($queue, $message)
    {
        try 
        {
            if(is_array($message) || is_object($message)) $message = json_encode($message);
        
            $connectionObject = self::getRabbitMQObject($queue);
            $msg = new \PhpAmqpLib\Message\AMQPMessage($message);
            
            $connectionObject->channel->basic_publish($msg, '', $queue);
        } 
        catch (\Exception $ex) 
        {
            $json = json_encode(
            [
                'mesaage' => $ex->getMessage(),
                'ex' => (array)$ex                
            ]);
            
            if(!strstr($ex->getMessage(), 'Channel connection is closed'))
                \Log::alert('Rabitt mesaj gönderilirken hata oluştu. (json:'.$json.')');
        }
    }
    
    public static function curl($url, $method = 'GET', $data = NULL, $headers = NULL, $cookieFileName = NULL)
    {
        send_log('info', 'Curl request starting', [$url]);
        
        $ch = curl_init(); 
        curl_setopt($ch,CURLOPT_URL, $url);
        
        if($headers != NULL)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if($method == 'POST')
        {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            
            if($data != NULL)
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if($cookieFileName)
        {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFileName);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFileName);
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
    
    public static function sendSms($tel, $message, $raw = FALSE) 
    {
        send_log('info', 'Send sms request', [$tel, $message]);
        
        $tel = str_replace(' ', '%20', $tel);
        
        $url = str_replace('$TEL', $tel, SMS_URL);
        $url = str_replace('$MESSAGE', urlencode($message), $url);
        
        self::$lastSmsResponse = self::curl($url);
        
        send_log('info', 'Send sms end', [self::$lastSmsResponse]);
        
        if($raw) return self::$lastSmsResponse;

        return self::$lastSmsResponse['error'] != FALSE;
    }
    
    public static function sendMail($subject, $html, $emailsWithNames, $attachments = [])
    {
        send_log('info', 'Send mail start', [$subject, $html, $emailsWithNames, $attachments]);

        Mail::send("email.base", ["html" => $html], function ($message) use($subject, $emailsWithNames, $attachments)
        {
            foreach($emailsWithNames as $emailsWithName)
                $message->to($emailsWithName['email'], $emailsWithName['name'])->subject($subject);
            
            foreach($attachments as $attachment)
                $message->attach($attachment);
        });

        $r = Mail::failures();
        send_log('info', 'Send mail OK', $r);
        
        return $r;
    }
    
    public static function fireBaseCloudMessaging($title, $text, $to = FB_BASE_TOPIC)
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
        
        return $data['error'] == FALSE;
    }
}