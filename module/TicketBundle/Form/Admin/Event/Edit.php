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

use Doctrine\ORM\EntityManager,
    TicketBundle\Component\Validator\Activity as ActivityValidator,
    TicketBundle\Entity\Event,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory,
    Zend\Form\Element\Submit;

/**
 * Edit Event
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Edit extends Add
{
    /**
     * @var \TicketBundle\Entity\Event
     */
    private $_event;

    /**
     * @param \TicketBundle\Entity\Event  $event
     * @param \Doctrine\ORM\EntityManager $entityManager The EntityManager instance
     * @param null|string|int             $name          Optional name for the element
     */
    public function __construct(Event $event, EntityManager $entityManager, $name = null)
    {
        $this->_event = $event;

        parent::__construct($entityManager, $name);

        $this->populateFromEvent($event);

        $field = new Submit('submit');
        $field->setValue('Save')
            ->setAttribute('class', 'edit');
        $this->add($field);
    }

    public function getInputFilterSpecification()
    {
        $inputs = parent::getInputFilterSpecification();

        foreach ($inputs as $key => $input) {
            if ($input['name'] == 'event') {
                $inputs[$key]['validators'] = array(
                    new ActivityValidator($this->_entityManager, $this->_event),
                );
                break;
            }
        }

        return $inputs;
    }
}
