<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use App\Repository\EventRepository;
use App\Entity\Task;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
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

    #[Route('/user/{id}', name: 'app_admin_user_report', methods:["GET"])]
    public function userReport(Request $request, UserRepository $userRepository, TaskRepository $taskRepository, $id): Response
    {
        $month=null;
        $tasks=[];

        if($request->get("month")>0 && $request->get("month")<13){
            $month = $request->get("month");
            $tasks = $taskRepository->findByMonth($request->get("month"), $this->getUser()->getId() );

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
            'filter' => $month,
        ]);
    }


    #[Route('/event/{id}', name: 'app_admin_event_report', methods:["GET"])]
    public function eventReport(TaskRepository $taskRepository, EventRepository $eventRepository, $id): Response
    {
           $event = $eventRepository->find($id);
            $tasks = $event->getTasks();
            $arrayTasks = $taskRepository->findBy(
                ["Event"=> $id],
                ["state"=>"ASC"]);

        return $this->render('admin/event.html.twig', [
            'tasks' => $arrayTasks,
        ]);
    }
    
}
