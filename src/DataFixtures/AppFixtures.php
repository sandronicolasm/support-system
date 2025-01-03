<?php

namespace App\DataFixtures;

use App\Entity\Urgency;
use App\Entity\ProblemType;
use App\Entity\TicketPriority;
use App\Entity\TicketStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $urgencies = ['Alta', 'Media', 'Baja'];
        foreach ($urgencies as $name) {
            $urgency = new Urgency();
            $urgency->setName($name);
            $manager->persist($urgency);
        }

        $problemTypes = ['Técnico', 'Administrativo', 'Otro'];
        foreach ($problemTypes as $name) {
            $problemType = new ProblemType();
            $problemType->setName($name);
            $manager->persist($problemType);
        }

        $status = ['Abierto', 'En progreso', 'Cerrado'];
        foreach ($status as $name) {
            $ticketStatus = new TicketStatus();
            $ticketStatus->setName($name);
            $manager->persist($ticketStatus);
        }

        $priorities = ['Alta', 'Media', 'Baja'];
        foreach ($priorities as $priority) {
            $ticketPriority = new TicketPriority();
            $ticketPriority->setName($priority);
            $manager->persist($ticketPriority);
        }

        $manager->flush();
    }
}
