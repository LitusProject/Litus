<?php

namespace ShiftBundle\Form\Admin\Shift;

/**
 * Export Shifts
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Export extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'event',
                'label'      => 'Event',
                'required'   => true,
                'attributes' => array(
                    'id'      => 'event',
                    'options' => $this->createEventsArray(),
                ),
            )
        );

        $this->addSubmit('Export', 'download');
    }

    private function createEventsArray()
    {
        $events = $this->getEntityManager()
            ->getRepository('CalendarBundle\Entity\Node\Event')
            ->findAllActive();

        $eventsArray = array();
        foreach ($events as $event) {
            $eventsArray[$event->getId()] = $event->getTitle();
        }

        return $eventsArray;
    }
}
