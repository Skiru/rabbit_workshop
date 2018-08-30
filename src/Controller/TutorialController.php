<?php

namespace App\Controller;

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
     * @Route("/two", name="tutorial_2")
     * @param WorkQueueService $service
     * @return Response
     */
    public function two(WorkQueueService $service):Response
    {
        $queue = 'task_queue';
        $service->setQueue($queue);
        $msg = $service->setMessage('Hello world');

        for($i=0; $i<50000; $i++)
        {
            $service
                ->getStreamSetup()
                ->getChannel()
                ->basic_publish($msg, '', $queue);
        }

        return new Response('tutorial 2');
    }
}