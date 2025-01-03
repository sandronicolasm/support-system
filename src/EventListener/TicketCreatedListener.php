<?php
namespace App\EventListener;

use App\Event\TicketCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


class TicketCreatedListener
{
    public function __construct(
        protected LoggerInterface $logger,
        protected HubInterface $hub
    ){
    }

    public function onTicketCreated(TicketCreatedEvent $event): void
    {
        $ticket = $event->getTicket();

        // Crea una actualización para Mercure
        $update = new Update(
            'tickets',
            json_encode([
                'id' => $ticket->getId(),
                'name' => $ticket->getName(),
                'urgency' => $ticket->getUrgency()->getName(),
                'problem_type' => $ticket->getProblemType()->getName(),
                'status' => $ticket->getStatus()->getName(),
                'created_at' => $ticket->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
        );
        // Ejemplo: Enviar notificación (puedes integrar un servicio de correo o mensajería aquí)
        $this->logger->info('Nuevo ticket creado', [
            'id' => $ticket->getId(),
            'name' => $ticket->getName(),
        ]);

        try {
            // Publicar la actualización
            $this->hub->publish($update);
        } catch (\Exception $e) {
            $this->logger->error('Error al publicar actualización en Mercure', [
                'exception' => $e,
            ]);
        }
    }
}