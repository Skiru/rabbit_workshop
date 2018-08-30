<?php

namespace App\Service;

interface RabbitMqInterface
{
    public function setQueue(string $queue);
    public function setMessage(string $message);
    public function runWorker(string $queue);
    public function setExchange(string $exchange, string $type);
}