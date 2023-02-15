<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\TaskRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        if($this->isGranted('ROLE_SUPER_ADMIN')){
            $events = $eventRepository->findAll();
        } else {
            $events =  $eventRepository->findBy(
                ['company' => $this->getUser()->getCompany()->getId()]
            );
        }
        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/event/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$event->getCompany()->getNif());
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/event/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new( Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $event->setCompany($this->getUser()->getCompany() );
        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/event/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$event->getCompany()->getNif());

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/event/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$event->getCompany()->getNif());

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/event/{id}/report', name: 'app_event_report', methods: ["GET"])]
    public function report(Event $event, TaskRepository $taskRepository, EventRepository $eventRepository, $id): Response
    {
        SecurityController::checkCompany($this, $this->getUser()->getCompany()->getNif(),$event->getCompany()->getNif());

        $tasks = $event->getTasks();
        $arrayTasks = $taskRepository->findBy(
            ["Event" => $id],
            ["state" => "ASC"]);

        return $this->render('admin/event.html.twig', [
            'tasks' => $arrayTasks,
        ]);
    }
}
