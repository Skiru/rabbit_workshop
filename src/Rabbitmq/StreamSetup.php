<?php

namespace App\Rabbitmq;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class StreamSetup
{
    /**
     * @var AMQPStreamConnection $connection
     */
    private $connection;

    /**
     * @var AMQPChannel $channel
     */
    private $channel;

    /**
     * StreamSetup constructor.
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'root');;
        $this->channel = $this->connection->channel();
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * @return AMQPStreamConnection
     */
    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }
}