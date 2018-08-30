<?php

namespace App\Service;

use App\Rabbitmq\StreamSetup;
use PhpAmqpLib\Message\AMQPMessage;

class PublishSubscribeService implements RabbitMqInterface
{
    /**
     * @var StreamSetup $streamSetup
     */
    private $streamSetup;

    /**
     * PublishSubscribeService constructor.
     * @param StreamSetup $streamSetup
     */
    public function __construct(StreamSetup $streamSetup)
    {
        $this->streamSetup = $streamSetup;
    }

    /**
     * @param string $queue
     * @return array
     */
    public function setQueue(string $queue)
    {
        return list($queue_name, ,) = $this->streamSetup->getChannel()->queue_declare("", false, false, true, false);
    }

    /**
     * @param string $message
     * @return AMQPMessage
     */
    public function setMessage(string $message)
    {
        return new AMQPMessage(
          "info: Hello world!"
        );
    }

    /**
     * @param string $exchange
     * @param string $type
     */
    public function setExchange(string $exchange, string $type)
    {
        $this->streamSetup
            ->getChannel()
            ->exchange_declare(
                'logs',
                'fanout',
                false,
                false,
                false
            );
    }

    /**
     * @param string $queue
     */
    public function runWorker(string $queue)
    {
        $this->streamSetup->getChannel()->queue_bind($queue, 'logs');

        $callback = function($msg) {
            echo '[x] Body: ', $msg->body, "\n";
        };

        $this->streamSetup->getChannel()->basic_consume($queue, '', false, true, false, false, $callback);

        register_shutdown_function(function (){
            $this->streamSetup->close();
        });

        while(count($this->streamSetup->getChannel()->callbacks)) {
            $this->streamSetup->getChannel()->wait();
        }
    }

    /**
     * @return StreamSetup
     */
    public function getStreamSetup(): StreamSetup
    {
        return $this->streamSetup;
    }
}