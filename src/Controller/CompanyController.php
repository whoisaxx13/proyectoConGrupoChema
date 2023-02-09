<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

#[Route('/company')]
class CompanyController extends AbstractController
{
    
    #[Route('/show', name: 'app_company_show', methods: ['GET'])]
    public function show(): Response
    {
        $company = $this->getUser()->getCompany();
        return $this->render('company/show.html.twig', [
            'company'=> $company,
        ]);
    }
}
