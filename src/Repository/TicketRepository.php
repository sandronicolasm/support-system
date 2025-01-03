<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * Get all tickets ordered by status, priority, and deadline.
     *
     * @return Ticket[]
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.status', 'ASC') // Ordenar por status, primero los de menor valor (status=1 aparecerá primero)
            ->addOrderBy('t.priority', 'DESC') // Luego por prioridad descendente
            ->addOrderBy('t.deadline', 'ASC') // Finalmente por fecha límite ascendente
            ->getQuery()
            ->getResult();
    }
}
