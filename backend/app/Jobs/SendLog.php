<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\User;

class SendLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $level, $message, $object;
    
    public function __construct($level, $message, $object = NULL)
    {
        $this->level = $level;
        $this->message = $message;
        $this->object = json_decode($object);
    }

    public function getLogLevelForLaravel()
    {
        switch($this->level)
        {
            case 'debug':
            case 'info':
            case 'warning':
                return $this->level;
            case 'danger':
                return 'alert';
        }
    }

    public function handle()
    {
        $log = helper('get_null_object');
        $log->message = $this->message;
        $log->obj = json_encode($this->object);

        \Log::{$this->getLogLevelForLaravel()}(json_encode($log));
    }
}