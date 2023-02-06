<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;

class MainController extends AbstractController
{
    #[Route('/main', name: 'app_main')]
    public function index(RoleHierarchyInterface $roleHierarchy): JsonResponse
    {
    }
}
