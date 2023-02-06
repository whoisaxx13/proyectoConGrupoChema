<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use App\Entity\Task;


#[Route('/admin')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'app_admin')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_admin_user_report', methods:["GET"])]
    public function userReport(Request $request, UserRepository $userRepository, TaskRepository $taskRepository, $id): Response
    {
        dd($this->isGranted("ROLE_PRUEBA"));
        $filter=$request->get("month");
        $tasks=[];

        if($filter>0 && $filter<13){
            $tasks = $taskRepository->findByMonth( $filter, $this->getUser()->getId() );

            //Conversion to Twig format.
            array_walk($tasks, function (& $item){
                $item['starttime'] = $item['start_time'];
                unset($item['start_time']);
                $item['endtime'] = $item['end_time'];
                unset($item['end_time']);
            });

        } else {
            $tasks = $this->getUser()->getTasks();
        }
        return $this->render('admin/show.html.twig', [
            'tasks' => $tasks,
        ]);
    }


    #[Route('/event/{id}', name: 'app_admin_event_report', methods:["GET"])]
    public function eventReport(Request $request, EventRepository $eventRepository, $dni): Response
    {


    }
    
}
