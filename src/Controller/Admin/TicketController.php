<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use App\Entity\TicketStatus;
use App\Message\EmailNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;

class TicketController extends AbstractController
{
    public function __construct(
        protected MessageBusInterface $messageBus)
    {
    }

    #[Route('/admin/tickets', name: 'admin_tickets')]
    public function index(EntityManagerInterface $em)
    {
        $tickets = $em->getRepository(Ticket::class)->findAll();
        $status = $em->getRepository(TicketStatus::class)->findAll();

        return $this->render('admin/tickets.html.twig', [
            'tickets' => $tickets,
            'status' => $status,
        ]);
    }

    #[Route('/admin/tickets/{id}/status', name: 'admin_ticket_status', methods: ['POST'])]
    public function updateStatus(Ticket $ticket, Request $request, EntityManagerInterface $em, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['status'])) {
            return new JsonResponse(['error' => 'Invalid status'], 400);
        }
        $statusOpen = $em->getRepository(TicketStatus::class)->find($data['status']);
        $ticket->setStatus($statusOpen); // Set the status to open to new tickets

        $em->persist($ticket);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'userEmail' => $ticket->getEmail(),
            'ticketStatus' => $ticket->getStatus()->getName(),
        ]);


        return new JsonResponse(['success' => false], 400);
    }

    #[Route('/admin/notification/send', name: 'admin_notification_send')]
    public function send(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['subject'], $data['body'])) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $notification = new EmailNotification($data['email'], $data['subject'], $data['body']);
        $this->messageBus->dispatch($notification);

        return new JsonResponse(['success' => true]);
    }
}