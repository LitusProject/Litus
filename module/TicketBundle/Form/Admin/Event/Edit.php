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

namespace TicketBundle\Form\Admin\Event;

use LogicException,
    TicketBundle\Component\Validator\Activity as ActivityValidator,
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

        if (!empty($this->event->getOptions())) {
            $this->get('enable_options')
                ->setAttribute('disabled', true);
        }

        $this->remove('submit');
        $this->addSubmit('Save', 'edit');

        $this->bind($this->event);
    }

    public function getInputFilterSpecification()
    {
        $inputs = parent::getInputFilterSpecification();

        foreach ($inputs as $key => $input) {
            if ($input['name'] == 'event') {
                $inputs[$key]['validators'] = array(
                    new ActivityValidator($this->geEntityManager(), $this->event),
                );
                break;
            }
        }

        return $inputs;
    }

    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }
}
