<?php

namespace App\Service;

use App\Entity\ProblemType;
use App\Entity\Ticket;
use App\Entity\TicketPriority;
use App\Entity\Urgency;

class TicketPriorityService
{
    public function assignPriority(Ticket $ticket): string
    {
        // Reglas de negocio para asignar prioridad
        if ($ticket->getUrgency()->getId() === Urgency::HIGH) {
            return TicketPriority::HIGH;
        }

        if ($ticket->getProblemType()->getId() === ProblemType::TECHNICAL && $ticket->getUrgency()->getId() === Urgency::MEDIUM) {
            return TicketPriority::HIGH;
        }

        // if ($ticket->getDeadline() && $ticket->getDeadline() < new \DateTime('+1 day')) {
        //     return TicketPriority::HIGH;
        // }

        if ($ticket->getUrgency() === 'media') {
            return TicketPriority::MEDIUM;
        }

        return TicketPriority::LOW;
    }
}