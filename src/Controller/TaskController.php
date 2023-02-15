<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    #[Route('/admin/task/{id}/resolve', name: 'app_task_resolve', methods: ["GET"])]
    public function resolve(Task $task, Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $state = $request->get("state");
        $coordinator = $request->get("coordinator");
        $driver = $request->get("driver");

        if(isset($state)){
            if($state == 1 || $state == 0){
                $task->setState($state);
                $entityManager->flush();
            }
        }
        if($coordinator && $driver){
            $task->setChore(["ROLE_DRIVER","ROLE_COORDINATOR"]);
            $entityManager->flush();
        } else if($coordinator){
            $task->setChore(["ROLE_COORDINATOR"]);
            $entityManager->flush();
        } else if($driver){
            $task->setChore(["ROLE_DRIVER"]);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_report', ["id" => $task->getEvent()->getId()], Response::HTTP_SEE_OTHER);
    }
}
