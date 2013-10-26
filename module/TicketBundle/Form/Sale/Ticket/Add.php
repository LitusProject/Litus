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

namespace TicketBundle\Form\Sale\Ticket;

use CommonBundle\Component\Form\Bootstrap\Element\Submit,
    CommonBundle\Component\Form\Bootstrap\Element\Select,
    CommonBundle\Component\Form\Bootstrap\Element\Collection,
    CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
    Doctrine\ORM\EntityManager,
    TicketBundle\Component\Validator\NumberTickets as NumberTicketsValidator,
    TicketBundle\Entity\Event,
    Zend\Form\Element\Hidden,
    Zend\InputFilter\InputFilter,
    Zend\InputFilter\Factory as InputFactory;

/**
 * Add Ticket
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
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
     * @param \TicketBundle\Entity\Event $event
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(EntityManager $entityManager, Event $event, $name = null)
    {
        parent::__construct($name);

        $this->_entityManager = $entityManager;
        $this->_event = $event;

        $this->setAttribute('id', 'ticket_sale_form');

        $field = new Checkbox('is_guest');
        $field->setLabel('Is Guest');
        $this->add($field);

        $personForm = new Collection('person_form');
        $personForm->setLabel('Person')
            ->setAttribute('id', 'person_form');
        $this->add($personForm);

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $personForm->add($field);

        $field = new Text('person');
        $field->setLabel('Person')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $personForm->add($field);

        $guestForm = new Collection('guest_form');
        $guestForm->setLabel('Guest')
            ->setAttribute('id', 'guest_form');
        $this->add($guestForm);

        $field = new Text('guest_first_name');
        $field->setLabel('First Name')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $guestForm->add($field);

        $field = new Text('guest_last_name');
        $field->setLabel('Last Name')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $guestForm->add($field);

        $field = new Text('guest_email');
        $field->setLabel('Email')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setRequired();
        $guestForm->add($field);

        $optionsForm = new Collection('options_form');
        $optionsForm->setLabel('Options');
        $this->add($optionsForm);

        if (count($event->getOptions()) == 0) {
            $field = new Select('number_member');
            $field->setLabel('Number Member')
                ->setAttribute('options', $this->_getNumberOptions())
                ->setAttribute('class', $field->getAttribute('class') . ' ticket_option')
                ->setAttribute('data-price', $event->getPriceMembers());
            $optionsForm->add($field);

            if (!$event->isOnlyMembers()) {
                $field = new Select('number_non_member');
                $field->setLabel('Number Non Member')
                    ->setAttribute('options', $this->_getNumberOptions())
                    ->setAttribute('class', $field->getAttribute('class') . ' ticket_option')
                    ->setAttribute('data-price', $event->getPriceNonMembers());
                $optionsForm->add($field);
            }
        } else {
            foreach($event->getOptions() as $option) {
                $field = new Select('option_' . $option->getId() . '_number_member');
                $field->setLabel(ucfirst($option->getName()) . ' (Member)')
                    ->setAttribute('options', $this->_getNumberOptions())
                    ->setAttribute('class', $field->getAttribute('class') . ' ticket_option')
                    ->setAttribute('data-price', $option->getPriceMembers());
                $optionsForm->add($field);

                if (!$event->isOnlyMembers()) {
                    $field = new Select('option_' . $option->getId() . '_number_non_member');
                    $field->setLabel(ucfirst($option->getName()) . ' (Non Member)')
                        ->setAttribute('options', $this->_getNumberOptions())
                        ->setAttribute('class', $field->getAttribute('class') . ' ticket_option')
                        ->setAttribute('data-price', $option->getPriceNonMembers());
                    $optionsForm->add($field);
                }
            }
        }

        $field = new Checkbox('payed');
        $field->setLabel('Payed');
        $optionsForm->add($field);

        $field = new Submit('sale_tickets');
        $field->setValue('Sale');
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

        if (isset($this->data['is_guest']) && $this->data['is_guest']) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'guest_first_name',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'guest_last_name',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'guest_email',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'EmailAddress'),
                        ),
                    )
                )
            );
        } else {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'person_id',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'int',
                            ),
                        ),
                    )
                )
            );

            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'person',
                        'required' => true,
                        'filters'  => array(
                            array('name' => 'StringTrim'),
                        ),
                    )
                )
            );
        }

        if (count($this->_event->getOptions()) == 0) {
            $inputFilter->add(
                $factory->createInput(
                    array(
                        'name'     => 'number_member',
                        'required' => true,
                        'validators' => array(
                            new NumberTicketsValidator($this->_entityManager, $this->_event),
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
                                new NumberTicketsValidator($this->_entityManager, $this->_event),
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
                                new NumberTicketsValidator($this->_entityManager, $this->_event),
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
                                    new NumberTicketsValidator($this->_entityManager, $this->_event),
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
