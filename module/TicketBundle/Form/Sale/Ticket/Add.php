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

namespace TicketBundle\Form\Sale\Ticket;

use LogicException,
    TicketBundle\Component\Validator\NumberTickets as NumberTicketsValidator,
    TicketBundle\Entity\Event;

/**
 * Add Ticket
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Add extends \CommonBundle\Component\Form\Bootstrap\Form
{
    /**
     * @var Event
     */
    private $event;

    public function init()
    {
        if (null === $this->event) {
            throw new LogicException('Cannot create tickets for null event.');
        }

        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'force',
            'label' => 'Force (Ignore limits)',
            'attributes' => array(
                'id' => 'force',
            ),
        ));

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'is_guest',
            'label' => 'Is Guest',
            'attributes' => array(
                'id' => 'is_guest',
            ),
        ));

        $personForm = $this->addFieldset('Person', 'person_form');

        $personForm->add(array(
            'type'       => 'hidden',
            'name'       => 'person_id',
            'attributes' => array(
                'id' => 'personId',
            ),
        ));

        $personForm->add(array(
            'type'       => 'text',
            'name'       => 'person',
            'label'      => 'Person',
            'required'   => true,
            'attributes' => array(
                'autocomplete' => 'off',
                'data-provice' => 'typeahead',
                'id'           => 'personSearch',
            ),
        ));

        $guestForm = $this->addFieldset('Guest', 'guest_form');

        $guestForm->add(array(
                'type'     => 'text',
                'name'     => 'guest_first_name',
                'label'    => 'First Name',
                'required' => true,
                'attributes' => array(
                    'id' => 'guest_first_name',
                ),
            )
        );

        $guestForm->add(array(
            'type'     => 'text',
            'name'     => 'guest_last_name',
            'label'    => 'Last Name',
            'required' => true,
            'attributes' => array(
                'id' => 'guest_last_name',
            ),
        ));

        $guestForm->add(array(
            'type'     => 'text',
            'name'     => 'guest_email',
            'label'    => 'Email',
            'required' => true,
            'attributes' => array(
                'id' => 'guest_email',
            ),
        ));

        $optionsForm = $this->addFieldset('Options', 'options_form');

        if (empty($this->event->getOptions()->toArray())) {
            $optionsForm->add(array(
                'type'       => 'select',
                'name'       => 'number_member',
                'label'      => 'Number Member',
                'attributes' => array(
                    'class'      => 'ticket_option',
                    'id'         => 'number_member',
                    'data-price' => $this->event->getPriceMembers(),
                    'options'    => $this->getNumberOptions(),
                ),
            ));

            if (!$this->event->isOnlyMembers()) {
                $optionsForm->add(array(
                    'type'       => 'select',
                    'name'       => 'number_non_member',
                    'label'      => 'Number Non Member',
                    'attributes' => array(
                        'class'      => 'ticket_option',
                        'id'         => 'number_non_member',
                        'data-price' => $this->event->getPriceNonMembers(),
                        'options'    => $this->getNumberOptions(),
                    ),
                ));
            }
        } else {
            foreach ($this->event->getOptions() as $option) {
                $optionsForm->add(array(
                    'type'       => 'select',
                    'name'       => 'option_' . $option->getId() . '_number_member',
                    'label'      => ucfirst($option->getName()) . ' (Member)',
                    'attributes' => array(
                        'class'      => 'ticket_option',
                        'id'         => 'option_' . $option->getId() . '_number_member',
                        'data-price' => $option->getPriceMembers(),
                        'options'    => $this->getNumberOptions(),
                    ),
                ));

                if (!$this->event->isOnlyMembers()) {
                    $optionsForm->add(array(
                        'type'       => 'select',
                        'name'       => 'option_' . $option->getId() . '_number_non_member',
                        'label'      => ucfirst($option->getName()) . ' (Non Member)',
                        'attributes' => array(
                            'class'      => 'ticket_option',
                            'id'         => 'option_' . $option->getId() . '_number_non_member',
                            'data-price' => $option->getPriceNonMembers(),
                            'options'    => $this->getNumberOptions(),
                        ),
                    ));
                }
            }
        }

        $this->add(array(
            'type'  => 'checkbox',
            'name'  => 'payed',
            'label' => 'Payed',
            'attributes' => array(
                'id' => 'payed',
            ),
        ));

        $this->addSubmit('Sale', 'sale_tickets', 'submit', array('id' => 'sale_tickets'));
    }

    private function getNumberOptions()
    {
        $numbers = array();
        $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();

        for ($i = 0 ; $i <= $max ; $i++) {
            $numbers[$i] = $i;
        }

        return $numbers;
    }

    public function getInputFilterSpecification()
    {
        $inputFilter = array();

        $inputFilter[] = array(
            'name'     => 'force',
            'required' => false,
        );

        $inputFilter[] = array(
            'name'     => 'is_guest',
            'required' => false,
        );

        $isGuest = isset($this->data['is_guest']) && $this->data['is_guest'];
        $force = isset($this->data['force']) && $this->data['force'];

        $personForm = array(
            array(
                'name'     => 'person_id',
                'required' => !$isGuest,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'int',
                    ),
                ),
            ),
            array(
                'name'     => 'person',
                'required' => !$isGuest,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ),
        );

        $inputFilter['person_form'] = $this->getInputFilterFactory()
                ->createInputFilter($personForm);

        $guestForm = array(
            array(
                'name'     => 'guest_first_name',
                'required' => $isGuest,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            array(
                'name'     => 'guest_last_name',
                'required' => $isGuest,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
            ),
            array(
                'name'     => 'guest_email',
                'required' => $isGuest,
                'filters'  => array(
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array('name' => 'EmailAddress'),
                ),
            ),
        );

        $inputFilter['guest_form'] = $this->getInputFilterFactory()
                ->createInputFilter($guestForm);

        if (!$force) {
            $optionsForm = array();
            if (empty($this->event->getOptions())) {
                $optionsForm[] = array(
                    'name'     => 'number_member',
                    'required' => true,
                    'validators' => array(
                        new NumberTicketsValidator($this->getEntityManager(), $this->event),
                    ),
                );

                if (!$this->event->isOnlyMembers()) {
                    $optionsForm[] = array(
                        'name'     => 'number_non_member',
                        'required' => true,
                        'validators' => array(
                            new NumberTicketsValidator($this->getEntityManager(), $this->event),
                        ),
                    );
                }
            } else {
                foreach ($this->event->getOptions() as $option) {
                    $optionsForm[] = array(
                        'name'     => 'option_' . $option->getId() . '_number_member',
                        'required' => true,
                        'validators' => array(
                            new NumberTicketsValidator($this->getEntityManager(), $this->event),
                        ),
                    );

                    if (!$this->event->isOnlyMembers()) {
                        $optionsForm[] = array(
                            'name'     => 'option_' . $option->getId() . '_number_non_member',
                            'required' => true,
                            'validators' => array(
                                new NumberTicketsValidator($this->getEntityManager(), $this->event),
                            ),
                        );
                    }
                }
            }

            $inputFilter['options_form'] = $this->getInputFilterFactory()
                    ->createInputFilter($optionsForm);
        }

        return $inputFilter;
    }

    /**
     * @param  Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }
}
