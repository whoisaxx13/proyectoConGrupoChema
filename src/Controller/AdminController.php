<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TaskRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'app_admin', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, TaskRepository $taskRepository): Response
    {

        $filter_month = $request->get('mes');
        $filter_year = $request->get('año');

        if(!isset($filter_year)){
            $filter_year = date("Y");
        }

        if(!isset($filter_month)){
            $filter_month = (int) date('m') - 1;
            if(!isset($filter_year)) $filter_year = $filter_month == 12 ? (int) $filter_year -1 : $filter_year;
        }

        $filter = "$filter_year-" . ((int)$filter_month < 10 ? "0$filter_month" : $filter_month);

        $users = $userRepository->findByCompany($this->getUser()->getCompany());

        $user_logs = [];
        foreach ($users as $user) {

            $tasks = $taskRepository->findByDate($filter, $user);
            $salaryperhour = $user->getCompany()->getSalaryperhour();


            $hours = 0;
            $minutes = 0;

            foreach ($tasks as $task) {
                $chore = $task->getChore();

                if (in_array('ROLE_COORDINATOR', $chore)) $hours = 4;
                if (in_array('ROLE_COORDINATOR', $chore)) $hours = 6;

                $hours += $task->getStartTime()->diff($task->getEndTime())->h;
                $minutes += $task->getStartTime()->diff($task->getEndTime())->i;

            }

            $hours += floor($minutes / 60);
            $minutes = $minutes % 60;

            $objectUser = new \stdClass();
            $objectUser->id = $user->getId();
            $objectUser->fullname = $user->getFullName();
            $objectUser->hours = $hours;
            $objectUser->minutes = $minutes < 10 ? "0$minutes" : $minutes;
            $objectUser->date = "$filter_month/$filter_year";
            $objectUser->salary = $hours*$salaryperhour;

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
            'filter_month' => $filter_month,
        ]);
    }

}
