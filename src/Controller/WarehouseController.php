<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\EventRepository;
use App\Repository\TaskRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WarehouseController extends AbstractController
{
    #[Route('/warehouse', name: 'app_warehouse')]
    public function index(TaskRepository $taskRepository, EventRepository $eventRepository): Response
    {
        
        return $this->render('warehouse/index.html.twig', [
            'controller_name' => 'WarehouseController',
            'tareas' => $taskRepository->findAll(),
            'eventrep'=>$eventRepository,
            'horasmensuales'=>$taskRepository->getHorasRealizadas($taskRepository->getHorasMensuales(date('Y'), date('m')))
            
        ]);
    }
    #[Route('/warehouse/vacations', name: 'app_warehouse_vacation',  methods: ['GET', 'POST'])]
    public function vacation(Request $request, TaskRepository $taskRepository, EventRepository $eventRepository): Response
    {
        $fechainicio = new DateTime($request->get('date-i'));
        // $fechainicio =\DateTime::createFromFormat("d/m/Y H:i",strtotime($request->get('date-i')))->format("Y-m-d H:i:s");
        $fechafin = new DateTime($request->get('date-f'));
        $event = new Event();
        $event->setName("Vacaciones | ".$this->getUser()->getUsername());
        $event->setStartDate($fechainicio);
        $event->setEndDate($fechafin);
        $event->setHidden(0);
        $event->setSchedule(' ');
        $event->setLinkInformation(' ');
        $event->setWorkersNumber(1);
        $event->setHidden(1);
        $event->setCompany($this->getUser()->getCompany());
        
        $task = new Task();
        $task->setUser($this->getUser());
        $task->setStart_Time($fechainicio);
        $task->setEnd_Time($fechafin);
        $task->setEvent($event);
        $task->setType(2);
        
        // $task->setTaskId($taskRepository->findOneById(1));
            
        if($request->get('date-i')){
            $eventRepository->save($event, true);
            $taskRepository->save($task, true);
            return $this->redirectToRoute('app_warehouse', [
                'controller_name' => 'WarehouseController',
                'tareas' => $taskRepository->findAll(),
                'eventrep'=>$eventRepository,
                'horasmensuales'=>$taskRepository->getHorasRealizadas($taskRepository->getHorasMensuales(date('Y'), date('m')))
            ], Response::HTTP_SEE_OTHER);
        }else{

            return $this->render('warehouse/vacation.html.twig', [
                'task' => $task,
                // 'form' => $form,
            ]);
        }

    }
}
