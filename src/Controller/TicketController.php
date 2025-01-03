<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\TicketPriority;
use App\Entity\TicketStatus;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Event\TicketCreatedEvent;
use App\Service\TicketPriorityService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TicketController extends AbstractController
{
    #[Route('/', name: 'app_ticket')]
    public function index(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $statusOpen = $entityManager->getRepository(TicketStatus::class)->find(TicketStatus::STATUS_OPEN);



            $priorityService = new TicketPriorityService();
            $priority = $priorityService->assignPriority($ticket);
            $ticketPriority = $entityManager->getRepository(TicketPriority::class)->find($priority);

            $ticket->setStatus($statusOpen); // Set the status to open to new tickets
            $ticket->setPriority($ticketPriority);

            $entityManager->persist($ticket);
            $entityManager->flush();

            $eventDispatcher->dispatch(new TicketCreatedEvent($ticket), TicketCreatedEvent::NAME); // Dispatch the ticket to hub mercure

            $this->addFlash('success', '¡Ticket enviado con éxito!');
            return $this->redirectToRoute('app_ticket');
        }

        return $this->render('ticket/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}