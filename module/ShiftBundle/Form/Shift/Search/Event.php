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

namespace ShiftBundle\Form\Shift\Search;

use CommonBundle\Entity\General\Language,
    LogicException;

/**
 * Search Event
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Event extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var Language|null
     */
    private $language;

    public function __construct($name = null)
    {
        parent::__construct($name, false, false);
    }

    public function init()
    {
        if (null === $this->language) {
            throw new LogicException('Language needs to be set.');
        }

        parent::init();

        $this->setAttribute('class', 'form-inline');

        $this->add(array(
            'type'       => 'select',
            'name'       => 'event',
            'attributes' => array(
                'options' => $this->createEventsArray(),
            ),
            'options'    => array(
                'input' => array(
                    'required' => true,
                ),
            ),
        ));
    }

    /**
     * @param  Language $langauge
     * @return self
     */
    public function setLanguage(Language $language)
    {
        $this->language = $language;

        return $this;
    }

    private function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array(
            '' => ''
        );
        foreach ($events as $event)
            $eventsArray[$event->getId()] = $event->getTitle($this->language);

        return $eventsArray;
    }
}
