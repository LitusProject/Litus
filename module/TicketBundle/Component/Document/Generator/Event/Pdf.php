<?php

namespace TicketBundle\Component\Document\Generator\Event;

use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use DateTime;
use Doctrine\ORM\EntityManager;
use TicketBundle\Entity\Event;

/**
 * Pdf
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Pdf extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @param EntityManager $entityManager The EntityManager instance
     * @param Event         $event
     * @param TmpFile       $file          The file to write to
     */
    public function __construct(EntityManager $entityManager, Event $event, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('ticket.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/event/event.xsl',
            $file->getFilename()
        );

        $this->event = $event;
    }

    /**
     * Generate the XML for the fop.
     *
     * @param TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $now = new DateTime();
        $organization_short_name = $configs->getConfigValue('organization_short_name');
        $organization_name = $configs->getConfigValue('organization_name');
        $organization_logo = $configs->getConfigValue('organization_logo');

        $tickets = $this->getEntityManager()
            ->getRepository('TicketBundle\Entity\Ticket')
            ->findAllActiveByEvent($this->event);

        $list = array();

        foreach ($tickets as $ticket) {
            $node = new Node(
                'ticket',
                null,
                array(
                    new Node(
                        'name',
                        null,
                        $ticket->getFullName()
                    ),
                    new Node(
                        'status',
                        null,
                        $ticket->getStatus()
                    ),
                    new Node(
                        'option',
                        null,
                        ($ticket->getOption() ? $ticket->getOption()->getName() : '') . ' (' . ($ticket->isMember() ? 'Member' : 'Non Member') . ')'
                    ),
                    new Node(
                        'number',
                        null,
                        $ticket->getNumber()
                    ),
                    new Node(
                        'bookdate',
                        null,
                        $ticket->getBookDate() ? $ticket->getBookDate()->format('d/m/Y H:i') : ''
                    ),
                    new Node(
                        'solddate',
                        null,
                        $ticket->getSoldDate() ? $ticket->getSoldDate()->format('d/m/Y H:i') : ''
                    ),
                    new Node(
                        'member',
                        null,
                        $ticket->isMember() ? '1' : '0'
                    ),
                )
            );

            $list[] = $node;
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'event',
                array(
                    'name' => $this->event->getActivity()->getTitle(),
                    'date' => $now->format('d F Y'),
                ),
                array(
                    new Node(
                        'our_union',
                        array(
                            'short_name' => $organization_short_name,
                        ),
                        array(
                            new Node(
                                'name',
                                null,
                                $organization_name
                            ),
                            new Node(
                                'logo',
                                null,
                                $organization_logo
                            ),
                        )
                    ),
                    new Node(
                        'tickets',
                        null,
                        $list
                    ),
                )
            )
        );
    }
}
