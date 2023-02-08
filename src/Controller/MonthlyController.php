<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonthlyController extends AbstractController
{
    #[Route('/monthly', name: 'app_monthly')]
    public function index(TaskRepository $taskRepository): Response
    {
        $date = date('Y-m-d');
        $previousMonth = date('m', strtotime($date . ' - 1 month'));
        $previousYear = date('Y', strtotime($date . ' - 1 month'));
        return $this->render('monthly/index.html.twig', [
            'controller_name' => 'MonthlyController',
            'fecha' => date('m-Y'),
            'tareas' => $taskRepository->getHorasRealizadas($taskRepository->getHorasMensuales(date('Y'),date('m'))),
            'tareasAtrasadas' => $taskRepository->getHorasRealizadas($taskRepository->getHorasMensuales($previousYear,$previousMonth)),
        ]);
    }
}
