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

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Node,
    DateTime,
    Doctrine\ORM\EntityManager,
    TicketBundle\Entity\Event;

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
