<?php

namespace TicketBundle\Component\Document\Generator\Event;

use Doctrine\ORM\EntityManager;
use TicketBundle\Entity\Event;

/**
 * Csv
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Csv extends \CommonBundle\Component\Document\Generator\Csv
{
    /**
     * @param EntityManager $entityManager
     * @param Event         $event
     */
    public function __construct(EntityManager $entityManager, Event $event)
    {
        $headers = array('ID', 'Name', 'Status', 'Option', 'Number', 'Book Date', 'Sold Date', 'Member');

        $tickets = $entityManager
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllActiveByEvent($event);

        $result = array();
        foreach ($tickets as $ticket) {
            $result[] = array(
                $ticket->getId(),
                $ticket->getFullName(),
                $ticket->getStatus(),
                ($ticket->getOption() ? $ticket->getOption()->getName() : '') . ' (' . ($ticket->isMember() ? 'Member' : 'Non Member') . ')',
                $ticket->getNumber(),
                $ticket->getBookDate() ? $ticket->getBookDate()->format('d/m/Y H:i') : '',
                $ticket->getSoldDate() ? $ticket->getSoldDate()->format('d/m/Y H:i') : '',
                $ticket->isMember() ? '1' : '0',
            );
        }

        parent::__construct($headers, $result);
    }
}
