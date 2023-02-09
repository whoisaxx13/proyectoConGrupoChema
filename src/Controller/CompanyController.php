<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CompanyController extends AbstractController
{
    #[Route('/company/show/{nif}', name: 'app_company_show', methods: ['GET','POST'])]
    public function show(Company $company): Response
    {
        return $this->render('company/show.html.twig', [
            'company'=> $company,
        ]);
    }

    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/admin/company', name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        $companies = $companyRepository->findAll();
        return $this->render('company/index.html.twig', [
            'companies' => $companies,
        ]);
    }
    
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('admin/company/new', name: 'app_company_new', methods: ['GET','POST'])]
    public function new(Request $request, CompanyRepository $companyRepository): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $companyRepository->save($company, true);

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('company/new.html.twig', [
            'company'=> $company,
            'form'=> $form,
        ]);
    }
    #[IsGranted('ROLE_SUPER_ADMIN')]
    #[Route('/admin/company/{id}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, CompanyRepository $companyRepository): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $CompanyRepository->save($Company, true);

            return $this->redirectToRoute('app_company_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('company/edit.html.twig', [
            'Company' => $company,
            'form' => $form,
        ]);
    }
}
