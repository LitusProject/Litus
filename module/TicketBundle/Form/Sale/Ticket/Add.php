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
    CommonBundle\Component\Form\Bootstrap\Element\Checkbox,
    CommonBundle\Component\Form\Bootstrap\Element\Text,
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
     * @var \TicketBundle\Entity\Event
     */
    private $_event;

    /**
     * @param \TicketBundle\Entity\Event $event
     * @param null|string|int $name Optional name for the element
     */
    public function __construct(Event $event, $name = null)
    {
        parent::__construct($name);

        $this->_event = $event;

        $this->setAttribute('id', 'ticket_sale_form');

        $field = new Hidden('person_id');
        $field->setAttribute('id', 'personId');
        $this->add($field);

        $field = new Text('person');
        $field->setLabel('Person')
            ->setAttribute('class', $field->getAttribute('class') . ' input-xlarge')
            ->setAttribute('id', 'personSearch')
            ->setAttribute('autocomplete', 'off')
            ->setAttribute('data-provide', 'typeahead')
            ->setRequired();
        $this->add($field);

        $field = new Select('number_member');
        $field->setLabel('Number Member')
            ->setAttribute('options', $this->_getNumberOptions());
        $this->add($field);

        $field = new Select('number_non_member');
        $field->setLabel('Number Non Member')
            ->setAttribute('options', $this->_getNumberOptions());
        $this->add($field);

        $field = new Checkbox('payed');
        $field->setLabel('Payed');
        $this->add($field);

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

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'number_member',
                    'required' => true,
                    'validators' => array(
                        new NumberTicketsValidator($this->_event->getLimitPerPerson()),
                    )
                )
            )
        );

        $inputFilter->add(
            $factory->createInput(
                array(
                    'name'     => 'number_non_member',
                    'required' => true,
                    'validators' => array(
                        new NumberTicketsValidator($this->_event->getLimitPerPerson()),
                    )
                )
            )
        );

        return $inputFilter;
    }
}
