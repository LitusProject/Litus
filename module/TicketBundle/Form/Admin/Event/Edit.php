<?php

namespace TicketBundle\Form\Admin\Event;

use TicketBundle\Entity\Event;

/**
 * Edit Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends \TicketBundle\Form\Admin\Event\Add
{
    /**
     * @var Event
     */
    private $event;

    public function init()
    {
        parent::init();

        $events = $this->createEventsArray();
        $events[$this->event->getActivity()->getId()] = $this->event->getActivity()->getTitle();
        $this->get('event')->setAttribute('options', $events);

        if (!$this->event->getOptions()->isEmpty()) {
            $this->get('enable_options')
                ->setAttribute('disabled', true);
        }

        $this->remove('submit')
            ->addSubmit('Save', 'edit');

        $this->bind($this->event);
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        foreach ($specs as $key => $spec) {
            if (isset($spec['name']) && $spec['name'] == 'event') {
                $specs[$key]['validators'] = array(
                    array(
                        'name'    => 'Activity',
                        'options' => array(
                            'exclude' => $this->event,
                        ),
                    ),
                );
                break;
            }
        }

        return $specs;
    }

    /**
     * @param  \TicketBundle\Entity\Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }
}
