<?php

namespace App\Service;

use App\Rabbitmq\StreamSetup;
use PhpAmqpLib\Message\AMQPMessage;

class WorkQueueService
{
    /**
     * @var StreamSetup $streamSetup
     */
    private $streamSetup;

    /**
     * WorkQueueService constructor.
     * @param StreamSetup $streamSetup
     */
    public function __construct(StreamSetup $streamSetup)
    {
        $this->streamSetup = $streamSetup;
    }

    public function setQueue(string $queue)
    {
        $this->streamSetup->getChannel()->queue_declare($queue, false, true, false, false);

        return $queue;
    }

    public function setMessage(string $message)
    {
        return new AMQPMessage(
            $message,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
            ]
        );
    }

    /**
     * @return StreamSetup
     */
    public function getStreamSetup(): StreamSetup
    {
        return $this->streamSetup;
    }

    public function runWorker(string $queue)
    {
        $callback = function($msg) {
            echo '[x] Received', $msg->body, "\n";
//            sleep(substr_count($msg->body, '.'));
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $this->streamSetup->getChannel()->basic_qos(null, 1, null);
        $this->streamSetup->getChannel()->basic_consume($queue, '', false, false, false, false, $callback);

        register_shutdown_function(function (){
            $this->streamSetup->close();
        });

        while(count($this->streamSetup->getChannel()->callbacks)) {
            $this->streamSetup->getChannel()->wait();
        }
    }
}