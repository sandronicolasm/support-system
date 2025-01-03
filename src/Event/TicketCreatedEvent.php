<?php

namespace App\Event;

use App\Entity\Ticket;
use Symfony\Contracts\EventDispatcher\Event;

class TicketCreatedEvent extends Event
{
    public const NAME = 'ticket.created';

    private Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }
}