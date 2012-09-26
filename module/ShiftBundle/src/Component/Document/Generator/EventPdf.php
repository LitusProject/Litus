<?php
/**
 * Litus is a project by a group of students from the K.U.Leuven. The goal is to create
 * various applications to support the IT needs of student unions.
 *
 * @author Niels Avonds <niels.avonds@litus.cc>
 * @author Karsten Daemen <karsten.daemen@litus.cc>
 * @author Bram Gotink <bram.gotink@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 * @author Kristof Mariën <kristof.marien@litus.cc>
 *
 * @license http://litus.cc/LICENSE
 */

namespace ShiftBundle\Component\Document\Generator;

use CommonBundle\Component\Util\File\TmpFile,
    CommonBundle\Component\Util\Xml\Generator,
    CommonBundle\Component\Util\Xml\Object,
    CalendarBundle\Entity\Nodes\Event,
    ShiftBundle\Entity\Shift,
    DateTime,
    Doctrine\ORM\EntityManager;

/**
 * EventPdf
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class EventPdf extends \CommonBundle\Component\Document\Generator\Pdf
{
    /**
     * @var \CudiBundle\Entity\Stock\Order
     */
    private $_event;

    /**
     * @var array
     */
    private $_shifts;

    /**
     * Create a new Event PDF Generator.
     *
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param \CalendarBundle\Entity\Nodes\Event $event The event
     * @param array $shifts The shifts for this event
     * @param \CommonBundle\Component\Util\File\TmpFile $file The file to write to
     */
    public function __construct(EntityManager $entityManager, Event $event, array $shifts, TmpFile $file)
    {
        $filePath = $entityManager
            ->getRepository('CommonBundle\Entity\General\Config')
            ->getConfigValue('shift.pdf_generator_path');

           parent::__construct(
               $entityManager,
            $filePath . '/event/event.xsl',
            $file->getFilename()
        );

        $this->_shift = $shift;
    }

    /**
     * Generate the XML for the fop.
     *
     * @param \CommonBundle\Component\Util\TmpFile $tmpFile The file to write to.
     */
    protected function generateXml(TmpFile $tmpFile)
    {
        $configs = $this->getConfigRepository();

        $union_name = $configs->getConfigValue('union_name');
        $logo = $configs->getConfigValue('union_logo');
        foreach ($this->_shifts as $shift) {
            $people = 

            foreach ($shifts->getResponsibles() as $responsible) {
                new Object(
                    'shift',
                    array(),
                    array(
                        new Object(
                            'date',
                            array(),
                            $shift->getStart
                        ),
                        new Object(
                            'name',
                            array(),

                        ),
                        new Object(
                            'person',
                            array(),

                        ),
                        new Object(
                            'responsible',
                            array(),

                        ),
                    )
                )
            }
        }

        $xml = new Generator($tmpFile);

        $xml->append(
            new Object(
                'event',
                array(
                    'name' => $this->_event->getTitle();
                    'date' => $this->_event->getStartDate()->format('d F Y H:i');
                ),
                array(
                    new Object(
                        'our_union',
                        array(
                            new Object(
                                'name',
                                null,
                                $union_name
                            ),
                            new Object(
                                'logo',
                                null,
                                $logo
                            )
                        )
                    ),
                    new Object(
                        'shifts',
                        null,
                        $shifts
                    )
                )
            )
        );
    }
}
