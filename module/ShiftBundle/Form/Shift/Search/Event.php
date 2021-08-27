<?php

namespace ShiftBundle\Form\Shift\Search;

use CommonBundle\Entity\General\Language;
use LogicException;

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
        if ($this->language === null) {
            throw new LogicException('Language needs to be set.');
        }

        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'event',
                'attributes' => array(
                    'options' => $this->createEventsArray(),
                ),
                'options' => array(
                    'input' => array(
                        'required' => true,
                    ),
                ),
            )
        );

        $this->remove('csrf');
    }

    /**
     * @param  Language $language
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
            '' => '',
        );
        foreach ($events as $event) {
            $eventsArray[$event->getId()] = $event->getTitle($this->language);
        }

        return $eventsArray;
    }
}
