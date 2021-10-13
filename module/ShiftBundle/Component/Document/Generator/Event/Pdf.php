<?php

namespace ShiftBundle\Component\Document\Generator\Event;

use CalendarBundle\Entity\Node\Event;
use CommonBundle\Component\Util\File\TmpFile;
use CommonBundle\Component\Util\Xml\Generator;
use CommonBundle\Component\Util\Xml\Node;
use Doctrine\ORM\EntityManager;

/**
 * EventPdf
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Pdf extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var array
     */
    private $shifts;

    /**
     * Create a new Event PDF Generator.
     *
     * @param EntityManager $entityManager
     * @param Event         $event         The event
     * @param TmpFile       $file          The file to write to
     */
    public function __construct(EntityManager $entityManager, Event $event, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.pdf_generator_path');

        parent::__construct(
            $entityManager,
            $filePath . '/event/event.xsl',
            $file->getFilename()
        );

        $this->event = $event;
        $this->shifts = $this->getEntityManager()
            ->getRepository('ShiftBundle\Entity\Shift')
            ->findBy(array('event' => $event), array('startDate' => 'ASC'));
    }

    /**
     * Generate the XML for FOP.
     *
     * @param TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $organization_name = $configs->getConfigValue('organization_name');
        $organization_logo = $configs->getConfigValue('organization_logo');

        $shifts = array();
        foreach ($this->shifts as $shift) {
            $people = array();

            foreach ($shift->getResponsibles() as $responsible) {
                $people[] = new Node(
                    'person',
                    array(),
                    array(
                        new Node(
                            'name',
                            array(),
                            $responsible->getPerson()->getFullName()
                        ),
                        new Node(
                            'phone_number',
                            array(),
                            $responsible->getPerson()->getPhoneNumber()
                        ),
                        new Node(
                            'responsible',
                            array(),
                            '1'
                        ),
                    )
                );
            }
            if (count($shift->getResponsibles()) != $shift->getNbResponsibles()) {
                $y = $shift->getNbResponsibles() - count($shift->getResponsibles());
                for ($x = 0; $x < $y; $x++) {
                    $people[] = new Node(
                        'person',
                        array(),
                        array(
                            new Node(
                                'name',
                                array(),
                                ''
                            ),
                            new Node(
                                'phone_number',
                                array(),
                                ''
                            ),
                            new Node(
                                'responsible',
                                array(),
                                '1'
                            ),
                        )
                    );
                }
            }

            foreach ($shift->getVolunteers() as $volunteer) {
                $people[] = new Node(
                    'person',
                    array(),
                    array(
                        new Node(
                            'name',
                            array(),
                            $volunteer->getPerson()->getFullName()
                        ),
                        new Node(
                            'phone_number',
                            array(),
                            $volunteer->getPerson()->getPhoneNumber()
                        ),
                        new Node(
                            'responsible',
                            array(),
                            '0'
                        ),
                    )
                );
            }
            if (count($shift->getVolunteers()) != $shift->getNbVolunteers()) {
                $y = $shift->getNbVolunteers() - count($shift->getVolunteers());
                for ($x = 0; $x < $y; $x++) {
                    $people[] = new Node(
                        'person',
                        array(),
                        array(
                            new Node(
                                'name',
                                array(),
                                ''
                            ),
                            new Node(
                                'phone_number',
                                array(),
                                ''
                            ),
                            new Node(
                                'responsible',
                                array(),
                                '0'
                            ),
                        )
                    );
                }
            }
            $shifts[] = new Node(
                'shift',
                array(),
                array(
                    new Node(
                        'date',
                        array(),
                        $shift->getStartDate()->format('d/m/Y H:i') . '-' . $shift->getEndDate()->format('H:i')
                    ),
                    new Node(
                        'name',
                        array(),
                        $shift->getName()
                    ),
                    new Node(
                        'manager',
                        array(),
                        $shift->getManager()->getFullName()
                    ),
                    new Node(
                        'people',
                        array(),
                        $people
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Node(
                'event',
                array(
                    'name' => $this->event->getTitle(),
                    'date' => $this->event->getStartDate()->format('d F Y H:i'),
                ),
                array(
                    new Node(
                        'our_union',
                        array(),
                        array(
                            new Node(
                                'name',
                                array(),
                                $organization_name
                            ),
                            new Node(
                                'logo',
                                array(),
                                $organization_logo
                            ),
                        )
                    ),
                    new Node(
                        'shifts',
                        array(),
                        $shifts
                    ),
                )
            )
        );
    }
}
