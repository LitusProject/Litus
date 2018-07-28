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

namespace TicketBundle\Form\Admin\Event;

use LogicException,
    TicketBundle\Entity\Event;

/**
 * Edit Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var Event
     */
    private $event;

    public function init()
    {
        if (null === $this->event) {
            throw new LogicException('No event given to edit');
        }

        parent::init();

        $events = $this->createEventsArray();
        $events[$this->event->getActivity()->getId()] = $this->event->getActivity()->getTitle();
        $this->get('event')->setAttribute('options', $events);

        if (!$this->event->getOptions()->isEmpty()) {
            $this->get('enable_options')
                ->setAttribute('disabled', true);
        }

        $this->remove('submit');
        $this->addSubmit('Save', 'edit');

        $this->bind($this->event);
    }

    public function getInputFilterSpecification()
    {
        $specs = parent::getInputFilterSpecification();

        foreach ($specs as $key => $spec) {
            if (isset($spec['name']) && $spec['name'] == 'event') {
                $specs[$key]['validators'] = array(
                    array(
                        'name'    => 'ticket_activtiy',
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
