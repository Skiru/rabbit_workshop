<?php

namespace App\Command;

use App\Service\WorkQueueService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateConsumerCommand extends Command
{
    /**
     * @var WorkQueueService $workQueueService
     */
    private $workQueueService;

    /**
     * CreateConsumerCommand constructor.
     * @param WorkQueueService $workQueueService
     */
    public function __construct(WorkQueueService $workQueueService)
    {
        $this->workQueueService = $workQueueService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('worker:t2:run');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Worker is now running (CRTL + C to kill)');
        $queue = $this->workQueueService->setQueue('task_queue');
        $this->workQueueService->runWorker($queue);
    }
}