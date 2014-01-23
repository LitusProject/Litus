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

namespace TicketBundle\Form\Ticket;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Entity\User\Person,
    Doctrine\ORM\EntityManager,
    TicketBundle\Component\Validator\NumberTickets as NumberTicketsValidator,
    TicketBundle\Entity\Event,
    Zend\Form\Element\Hidden,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Book Tickets
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Book extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_entityManager;

    /**
     * @var \TicketBundle\Entity\Event
     */
    private $_event;

    /**
     * @var \CommonBundle\Entity\User\Person
     */
    private $_person;

    /**
     * @param \TicketBundle\Entity\Event $event
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Event $event, Person $person, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_event = $event;
        $this->_person = $person;

        $this->setAttribute('id', 'ticket_sale_form');

        if (count($event->getOptions()) == 0) {
            $field = new Select('number_member');
            $field->setLabel('Number Member')
                ->setAttribute('options', $this->_getNumberOptions());
            $this->add($field);

            if (!$event->isOnlyMembers()) {
                $field = new Select('number_non_member');
                $field->setLabel('Number Non Member')
                    ->setAttribute('options', $this->_getNumberOptions());
                $this->add($field);
            }
        } else {
            foreach($event->getOptions() as $option) {
                $field = new Select('option_' . $option->getId() . '_number_member');
                $field->setLabel(ucfirst($option->getName()) . ' (Member)')
                    ->setAttribute('options', $this->_getNumberOptions());
                $this->add($field);

                if (!$event->isOnlyMembers()) {
                    $field = new Select('option_' . $option->getId() . '_number_non_member');
                    $field->setLabel(ucfirst($option->getName()) . ' (Non Member)')
                        ->setAttribute('options', $this->_getNumberOptions());
                    $this->add($field);
                }
            }
        }

        $field = new Submit('book_tickets');
        $field->setValue('Book');
        $this->add($field);
    }

    private function _getNumberOptions()
    {
        $numbers = array();
        $max = $this->_event->getLimitPerPerson() == 0 ? 10 : $this->_event->getLimitPerPerson();

        for($i = 0 ; $i <= $max ; $i++) {
            $numbers[$i] = $i;
        }
        return $numbers;
    }

    public function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        if (count($this->_event->getOptions()) == 0) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'number_member',
                        'required' => true,
                        'validators' => array(
                            new NumberTicketsValidator($this->_entityManager, $this->_event, $this->_person),
                        )
                    )
                )
            );

            if (!$this->_event->isOnlyMembers()) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'number_non_member',
                            'required' => true,
                            'validators' => array(
                                new NumberTicketsValidator($this->_entityManager, $this->_event, $this->_person),
                            )
                        )
                    )
                );
            }
        } else {
            foreach($this->_event->getOptions() as $option) {
                $inputFilter->add(
                    $factory->createInput(
                        array(
                            'name'     => 'option_' . $option->getId() . '_number_member',
                            'required' => true,
                            'validators' => array(
                                new NumberTicketsValidator($this->_entityManager, $this->_event, $this->_person),
                            )
                        )
                    )
                );

                if (!$this->_event->isOnlyMembers()) {
                    $inputFilter->add(
                        $factory->createInput(
                            array(
                                'name'     => 'option_' . $option->getId() . '_number_non_member',
                                'required' => true,
                                'validators' => array(
                                    new NumberTicketsValidator($this->_entityManager, $this->_event, $this->_person),
                                )
                            )
                        )
                    );
                }
            }
        }

        return $inputFilter;
    }
}
