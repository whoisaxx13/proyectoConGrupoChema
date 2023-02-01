<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll();

        $user_logs = [];

        foreach ($users as $user){
            $tasks = $user->getTasks();

            $hours = 0;
            $minutes = 0;

            foreach ($tasks as $task){
                $minutes += ($task->getStartTime()->diff($task->getEndTime())->h * 60);
                $minutes += $task->getStartTime()->diff($task->getEndTime())->i;

                $hours = $hours + floor($minutes / 60);
                $minutes = $minutes +$minutes % 60;
            }


            $objectUser = new \stdClass();
            $objectUser->id = $user->getId();
            $objectUser->fullname = $user->getFullName();
            $objectUser->hours = $hours;
            $objectUser->minutes = $minutes;

            $user_logs[] = $objectUser;

        }




        return $this->render('admin/index.html.twig', [
            'trabajadores' => $user_logs,
        ]);
    }
}
