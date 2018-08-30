<?php

namespace App\Command;

use App\Service\PublishSubscribeService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateTutorialThreeConsumer extends Command
{
    /**
     * @var PublishSubscribeService $publishSubscribe]
     */
    private $publishSubscribe;

    /**
     * CreateTutorialThreeConsumer constructor.
     * @param PublishSubscribeService $publishSubscribe
     */
    public function __construct(PublishSubscribeService $publishSubscribe)
    {
        $this->publishSubscribe = $publishSubscribe;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('worker:t3:run');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Worker is now running (CRTL + C to kill)');
        $queue = $this->publishSubscribe->setQueue("this_name_doesnt_matter")[0];
        $this->publishSubscribe->setExchange('logs', 'fanout');
        $this->publishSubscribe->runWorker($queue);
    }
}