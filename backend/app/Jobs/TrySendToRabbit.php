<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Libraries\MessageLibrary;


class TrySendToRabbit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $queue, $message;
    
    public function __construct($queue, $message)
    {
        $this->queue = $queue;
        $this->message = $message;
    }

    public function handle()
    {
        MessageLibrary::sendToRabbitMQ($this->queue, $this->message);
    }
}