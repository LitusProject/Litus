<?php

namespace TicketBundle\Form\Ticket;

use LogicException;
use TicketBundle\Entity\Event;
use TicketBundle\Entity\GuestInfo;
use Zend\Validator\Identical;

class Bookguest extends \CommonBundle\Component\Form\Bootstrap\Form
{
    const FILE_SIZE = '5MB';

    /**
     * @var Event
     */
    private $event;

    /**
     * @var GuestInfo
     */
    private $guestInfo;

    /**
     * @var boolean Are the conditions already checked or not
     */
    protected $conditionsChecked = false;

    public function init()
    {
        if ($this->event == null) {
            throw new LogicException('Cannot book ticket for null form.');
        }

        parent::init();

        $this->setAttribute('id', 'ticket_sale_form');

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'guest_form',
                'label'    => 'Contact Details',
                'elements' => array(
                    array(
                        'type'       => 'text',
                        'name'       => 'guest_first_name',
                        'label'      => 'First Name',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'guest_first_name',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'guest_last_name',
                        'label'      => 'Last Name',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'guest_last_name',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'guest_email',
                        'label'      => 'Email',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'guest_email',
                        ),
                        'options'    => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'EmailAddress'),
                                ),
                            ),
                        ),
                    ),
                    // array(
                    //     'type'       => 'text',
                    //     'name'       => 'guest_identification',
                    //     'label'      => 'R-Number (optional)',
                    //     'required'   => false,
                    //     'attributes' => array(
                    //         'id' => 'guest_identification',
                    //     ),
                    //     'options'    => array(
                    //         'input' => array(
                    //             'filters' => array(
                    //                 array('name' => 'StringTrim'),
                    //             ),
                    //             'validators' => array(
                    //                 array('name' => 'UniversityIdentification'),
                    //             ),
                    //         ),
                    //     ),
                    // ),
                    // array(
                    //     'type'       => 'text',
                    //     'name'       => 'guest_organization',
                    //     'label'      => 'Kring (optional)',
                    //     'required'   => false,
                    //     'attributes' => array(
                    //         'id' => 'guest_organization',
                    //     ),
                    //     'options'    => array(
                    //         'input' => array(
                    //             'filters' => array(
                    //                 array('name' => 'StringTrim'),
                    //             ),
                    //         ),
                    //     ),
                    // ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'spacer',
                'label'    => 'Tickets',
                'elements' => array(
                    // intentionally empty
                ),
            )
        );

        if ($this->event->getOptions()->isEmpty()) {
            $this->add(
                array(
                    'type'       => 'select',
                    'name'       => 'number_member',
                    'label'      => 'Number Member',
                    'attributes' => array(
                        'options' => $this->getNumberOptions(),
                    ),
                    'options'    => array(
                        'input' => array(
                            'required' => true,
                            'validators' => array(
                                array(
                                    'name'    => 'NumberTicketsGuest',
                                    'options' => array(
                                        'event'   => $this->event,
                //                                        'person'  => $this->guestInfo,
                                        'maximum' => $this->event->getLimitPerPerson(),
                                    ),
                                ),
                            ),
                        ),
                    ),
                )
            );

            if (!$this->event->isOnlyMembers()) {
                $this->add(
                    array(
                        'type'       => 'select',
                        'name'       => 'number_non_member',
                        'label'      => 'Number Non Member',
                        'attributes' => array(
                            'options' => $this->getNumberOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required' => true,
                                'validators' => array(
                                    array(
                                        'name'    => 'NumberTicketsGuest',
                                        'options' => array(
                                            'event'   => $this->event,
                    //                                            'person'  => $this->guestInfo,
                                            'maximum' => $this->event->getLimitPerPerson(),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    )
                );
            }
        } else {
            foreach ($this->event->getOptions() as $option) {
                if ($option->isVisible()) {
                    $this->add(
                        array(
                            'type' => 'select',
                            'name' => 'option_' . $option->getId() . '_number_member',
                            'label' => $option->getPriceNonMembers() != 0 ? ucfirst($option->getName()) . ' (Member)' : ucfirst($option->getName()),
                            'attributes' => array(
//                                'options' => $this->getNumberOptions(),
                                'options' => $this->getLimitForOption($option),
                            ),
                            'options' => array(
                                'input' => array(
                                    'required' => true,
                                    'validators' => array(
                                        array(
                                            'name' => 'NumberTicketsGuest',
                                            'options' => array(
                                                'event' => $this->event,
                                                //                                            'person'  => $this->guestInfo,
                                                'maximum' => $this->event->getLimitPerPerson(),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        )
                    );

                    if (!$this->event->isOnlyMembers() && $option->getPriceNonMembers() != 0) {
                        $this->add(
                            array(
                                'type' => 'select',
                                'name' => 'option_' . $option->getId() . '_number_non_member',
                                'label' => ucfirst($option->getName()) . ' (Non Member)',
                                'attributes' => array(
//                                    'options' => $this->getNumberOptions(),
                                    'options' => $this->getLimitForOption($option),
                                ),
                                'options' => array(
                                    'input' => array(
                                        'required' => true,
                                        'validators' => array(
                                            array(
                                                'name' => 'NumberTicketsGuest',
                                                'options' => array(
                                                    'event' => $this->event,
                                                    //                                                'person'  => $this->guestInfo,
                                                    'maximum' => $this->event->getLimitPerPerson(),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            )
                        );
                    }
                }
            }
        }

//        $this->add(
//            array(
//                'type'       => 'checkbox',
//                'name'       => 'images',
//                'label'      => 'Hierbij geef ik toestemming om beeldmateriaal van mij te maken en te gebruiken (indien een gepubliceerde foto niet gewenst is kan je een mail sturen naar communicatie@vtk.be)',
//                'attributes' => array(
//                    'id' => 'images',
//                ),
//                'options'    => array(
//                    'input' => array(
//                        'validators' => array(
//                            array(
//                                'name'    => 'identical',
//                                'options' => array(
//                                    'token'    => true,
//                                    'strict'   => false,
//                                    'messages' => array(
//                                        Identical::NOT_SAME => 'You must agree to the terms and conditions.',
//                                    ),
//                                ),
//                            ),
//                        ),
//                    ),
//                ),
//            )
//        );

//        $this->add(
//            array(
//                'type'       => 'checkbox',
//                'name'       => 'conditions',
//            //                'label'      => 'I have read and accept the GDPR terms and condition specified above',
//                'label'      => 'Bij deze ga ik akkoord dat VTK mijn gegevens mag gebruiken voor de werking van deze activiteit, om te gebruiken voor noodgevallen en mij te contacteren.',
//                'attributes' => array(
//                    'id' => 'conditions',
//                ),
//                'options'    => array(
//                    'input' => array(
//                        'validators' => array(
//                            array(
//                                'name'    => 'identical',
//                                'options' => array(
//                                    'token'    => true,
//                                    'strict'   => false,
//                                    'messages' => array(
//                                        Identical::NOT_SAME => 'You must agree to the terms and conditions.',
//                                    ),
//                                ),
//                            ),
//                        ),
//                    ),
//                ),
//            )
//        );

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'conditions',
                'label'      => str_replace('%url%', $this->event->getTermsUrl(), $this->getServiceLocator()->get('translator')->translate('I have read and accept the terms and conditions specified here')),
                'attributes' => array(
                    'id' => 'conditions',
                ),
                'options'    => array(
                    'input' => array(
                        'validators' => array(
                            array(
                                'name'    => 'identical',
                                'options' => array(
                                    'token'    => true,
                                    'strict'   => false,
                                    'messages' => array(
                                        Identical::NOT_SAME => 'You must agree to the terms and conditions.',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Book', 'book_tickets');
    }

    private function getNumberOptions()
    {
        $numbers = array();
        $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();

        for ($i = 0; $i <= $max; $i++) {
            $numbers[$i] = $i;
        }

        return $numbers;
    }

    private function getLimitForOption(Event\Option $option) {
        $numbers = array();

        if ($option->getLimitPerPerson() == 0) {
            $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();
        } elseif ($option->getLimitPerPerson() > $this->event->getLimitPerPerson()) {
            $max = $this->event->getLimitPerPerson() == 0 ? 10 : $this->event->getLimitPerPerson();
        } else {
            $max = $option->getLimitPerPerson();
        }

        for ($i = 0; $i <= $max; $i++) {
            $numbers[$i] = $i;
        }

        return $numbers;
    }

    private function getStudiesOptions()
    {
        $options = array();

        $options['burgie'] = 'Ingenieurswetenschappen';
        $options['archie'] = 'Ingenieurswetenschappen: Architectuur';

        return $options;
    }

    private function getFoodOptions()
    {
        $options = array();

        $options['Vlees'] = 'Vlees';
        $options['Vegetariër'] = 'Vegetariër';

        return $options;
    }

    private function getTransportationOptions()
    {
        $options = array();

        $options['Eigen vervoer'] = 'Eigen vervoer';
        $options['Trein'] = 'Ik kom met de trein naar Genk, en wordt daar opgehaald met de VTK bus';

        return $options;
    }

    /**
     * @param Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @param boolean $conditionsChecked
     * @return self
     */
    public function setConditionsChecked($conditionsChecked = true)
    {
        $this->conditionsChecked = !!$conditionsChecked;

        return $this;
    }
}
