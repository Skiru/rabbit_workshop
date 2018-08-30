<?php

namespace App\Controller;

use App\Service\PublishSubscribeService;
use App\Service\WorkQueueService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutorialController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(): Response
    {
        return $this->render('tutorials/index.html.twig', []);
    }

    /**
     * @Route("/two/{iterations}", name="tutorial_2")
     * @param WorkQueueService $service
     * @param int $iterations
     * @return Response
     */
    public function two(WorkQueueService $service, int $iterations = 50000): Response
    {
        $queue = 'task_queue';
        $service->setQueue($queue);

        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $msg = $service->setMessage("Hello world {$i}..");
            $service
                ->getStreamSetup()
                ->getChannel()
                ->basic_publish($msg, '', $queue);
        }
        $stop = microtime(true);

        return $this->render('tutorials/index.html.twig', [
            'tutorial_number' => 2,
            'duration' => abs($stop - $start)
        ]);
    }

    /**
     * @Route("/three/{iterations}", name="tutorial_3")
     * @param PublishSubscribeService $service
     * @param int $iterations
     * @return Response
     */
    public function three(PublishSubscribeService $service, int $iterations = 50000): Response
    {
        $service->setExchange('logs', 'fanout');
        $start = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $msg = $service->setMessage("info: Hello world!");
            $service
                ->getStreamSetup()
                ->getChannel()
                ->basic_publish($msg, 'logs');
        }
        $stop = microtime(true);

        return $this->render('tutorials/index.html.twig', [
            'tutorial_number' => 3,
            'duration' => abs($stop - $start)
        ]);
    }
}