<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;


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

    #[Route('/user/{dni}', name: 'app_admin_user_report', methods:["GET"])]
    public function userReport(Request $request, UserRepository $userRepository, $dni): Response
    {
        $filter=$request->get("month");
        $tasksFiltered=[];

        $user = $userRepository->find($dni);
        $userTasks = $user->getTasks()->getValues();

        if($filter>0 && $filter<13){
            if($filter>0 && $filter<10){
                $filter="0".$filter;
            }
            for ($i=0; $i < count($userTasks); $i++) { 
                if($filter===$userTasks[$i]->getStartTime()->format("m")){
                    $tasksFiltered[]=$userTasks[$i];
                }
            }
        } else {
            $tasksFiltered = $userTasks;
        }
        $arrStart = [];
        for ($i=0; $i <count($tasksFiltered) ; $i++) { 
            $arrStart[]= $tasksFiltered[$i]->getStartTime();
        }
        array_multisort($arrStart, SORT_ASC, $tasksFiltered);

        return $this->render('admin/show.html.twig', [
            'user' => $user,
            'tasks' => $tasksFiltered,
            'startTime' => $arrStart,
        ]);
    }


    #[Route('/event/{id}', name: 'app_admin_event_report', methods:["GET"])]
    public function eventReport(Request $request, EventRepository $eventRepository, $dni): Response
    {


    }
    
}
