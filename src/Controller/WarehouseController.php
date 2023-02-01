<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        ]);
    }
}
