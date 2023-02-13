<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\Persistence\ManagerRegistry;

#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile(): Response
    {   
        return $this->render('user/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {   
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$user->getCompany()->getNif());

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/user/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $users = $userRepository->findAll();
        } else {
            $users = $userRepository->findBy(
                ['company' => $this->getUser()->getCompany()->getId()]
            );
        }
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/user/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$user->getCompany()->getNif());

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/user/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$user->getCompany()->getNif());

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/user/{id}/report', name: 'app_user_report', methods: ["GET"])]
    public function report(User $user, Request $request, UserRepository $userRepository, TaskRepository $taskRepository): Response
    {
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$user->getCompany()->getNif());

        $month=null;
        $tasks=[];

        if($request->get("month")>0 && $request->get("month")<13){
            $month = $request->get("month");
            $tasks = $taskRepository->findByMonth($request->get("month"), $user->getId());

            //Conversion to Twig format.
            array_walk($tasks, function (&$item) {
                $item['starttime'] = $item['start_time'];
                unset($item['start_time']);
                $item['endtime'] = $item['end_time'];
                unset($item['end_time']);
            });

        }else {
            $tasks = $user->getTasks();
        }

        return $this->render('admin/user.html.twig', [
            'user' => $user,
            'tasks' => $tasks,
            'filter' => $month,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/user/{id}/verify', name: 'app_user_verify', methods: ["GET"])]
    public function verify(User $user, Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$user->getId()
            );
        } else if(!in_array('ROLE_USER',$user->getRoles())){
            $user->setRoles(["ROLE_USER"]);
            $entityManager->flush();
        }

        return $this->redirectToRoute("app_user_index");
    }
}
