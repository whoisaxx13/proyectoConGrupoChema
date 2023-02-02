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

    #[Route('/', name: 'app_admin', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {

        $filter_month = $request->get('mes');
        $filter_year = $request->get('año');
        $users = $userRepository->findAll();

        $user_logs = [];

        foreach ($users as $user){
            $tasks = $user->getTasks();

            $hours = 0;
            $minutes = 0;


            foreach ($tasks as $task){

                if(!isset($filter_year)){
                    $filter_year = date("Y");
                }

                if(!isset($filter_month)){
                    $filter_month = (int) date('m') - 1;
                    if(!isset($filter_year)) $filter_year = $filter_month == 12 ? (int) $filter_year -1 : $filter_year;
                }

                if($filter_month == $task->getStartTime()->format('m') && $filter_year == $task->getStartTime()->format('Y')){
                    $hours += $task->getStartTime()->diff($task->getEndTime())->h;
                    $minutes += $task->getStartTime()->diff($task->getEndTime())->i;
                }
            }

            $hours += floor($minutes / 60);
            $minutes = $minutes % 60;

            $objectUser = new \stdClass();
            $objectUser->id = $user->getId();
            $objectUser->fullname = $user->getFullName();
            $objectUser->hours = $hours;
            $objectUser->minutes = $minutes < 10 ? "0$minutes" : $minutes;
            $objectUser->date = "$filter_month/$filter_year";

            $user_logs[] = $objectUser;

        }

        $years = [];

        $months_tmp = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $currentYear = date('Y');
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[] = $i;
        }

        return $this->render('admin/index.html.twig', [
            'trabajadores' => $user_logs,
            'years' => $years,
            'meses' => $months_tmp,
            'filter_month' => $filter_month
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
