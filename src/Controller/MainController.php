<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(UserRepository $userRepository): Response
    {
        if($this->getUser()){
            $user = $this->getUser();
            $user->setLastLogin(new \DateTime());
            $userRepository->save($user, true);
            $user->changeRemainingHours();
        }
        return $this->render('/main.html.twig', [
            
        ]);
    }
}
