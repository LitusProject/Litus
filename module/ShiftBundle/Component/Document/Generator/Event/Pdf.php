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
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Component\Document\Generator\Event;

use CalendarBundle\Entity\Node\Event,
    CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    Doctrine\ORM\EntityManager;

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
                $people[] = new Object(
                    'person',
                    array(),
                    array(
                        new Object(
                            'name',
                            array(),
                            $responsible->getPerson()->getFullName()
                        ),
                        new Object(
                            'phone_number',
                            array(),
                            $responsible->getPerson()->getPhoneNumber()
                        ),
                        new Object(
                            'responsible',
                            array(),
                            '1'
                        ),
                    )
                );
            }

            foreach ($shift->getVolunteers() as $volunteer) {
                $people[] = new Object(
                    'person',
                    array(),
                    array(
                        new Object(
                            'name',
                            array(),
                            $volunteer->getPerson()->getFullName()
                        ),
                        new Object(
                            'phone_number',
                            array(),
                            $volunteer->getPerson()->getPhoneNumber()
                        ),
                        new Object(
                            'responsible',
                            array(),
                            '0'
                        ),
                    )
                );
            }

            $shifts[] = new Object(
                'shift',
                array(),
                array(
                    new Object(
                        'date',
                        array(),
                        $shift->getStartDate()->format('d/m/Y H:i') . '-' . $shift->getEndDate()->format('H:i')
                    ),
                    new Object(
                        'name',
                        array(),
                        $shift->getName()
                    ),
                    new Object(
                        'manager',
                        array(),
                        $shift->getManager()->getFullName()
                    ),
                    new Object(
                        'people',
                        array(),
                        $people
                    ),
                )
            );
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'event',
                array(
                    'name' => $this->event->getTitle(),
                    'date' => $this->event->getStartDate()->format('d F Y H:i'),
                ),
                array(
                    new Object(
                        'our_union',
                        array(),
                        array(
                            new Object(
                                'name',
                                array(),
                                $organization_name
                            ),
                            new Object(
                                'logo',
                                array(),
                                $organization_logo
                            ),
                        )
                    ),
                    new Object(
                        'shifts',
                        array(),
                        $shifts
                    ),
                )
            )
        );
    }
}
