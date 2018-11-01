<?php
/**
 * Litus is a project by a group of students from the KU Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Koen Certyn <koen.certyn@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Dario Incalza <dario.incalza@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Lars Vierbergen <lars.vierbergen@litus.cc>
 * @author Daan Wendelen <daan.wendelen@litus.cc>
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 * @author Floris Kint <floris.kint@vtk.be>
 *
 * @license http://litus.cc/LICENSE
 */

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
