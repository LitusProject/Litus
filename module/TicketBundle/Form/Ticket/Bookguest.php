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
                    array(
                        'type'       => 'text',
                        'name'       => 'phone_number',
                        'label'      => 'Telefoonnummer',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'phone_number',
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
                        'name'       => 'address',
                        'label'      => 'Adres',
                        'required'   => true,
                        'attributes' => array(
                            'id' => 'address',
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
                        'type'       => 'select',
                        'name'       => 'studies',
                        'label'      => 'Toekomstige Studie',
                        'attributes' => array(
                            'options' => $this->getStudiesOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required' => true,
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'select',
                        'name'       => 'food_option',
                        'label'      => 'Eetgewoonte',
                        'attributes' => array(
                            'options' => $this->getFoodOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required' => true,
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'allergies',
                        'label'      => 'Allergieën',
                        'required'   => false,
                        'attributes' => array(
                            'id' => 'allergies',
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
                        'type'       => 'select',
                        'name'       => 'transportation',
                        'label'      => 'Vervoer',
                        'attributes' => array(
                            'options' => $this->getTransportationOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required' => true,
                            ),
                        ),
                    ),
                    array(
                        'type'       => 'text',
                        'name'       => 'comments',
                        'label'      => 'Opmerkingen',
                        'required'   => false,
                        'attributes' => array(
                            'id' => 'comments',
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
                        'type'       => 'file',
                        'name'       => 'picture',
                        'label'      => 'Recente foto',
                        'required'   => true,
                        'attributes' => array(
                            'data-help' => 'The maximum file size is ' . self::FILE_SIZE . '.',
                        ),
                        'options'    => array(
                            'input' => array(
                                'validators' => array(
                                    array(
                                        'name'    => 'FileSize',
                                        'options' => array(
                                            'max' => self::FILE_SIZE,
                                        ),
                                    ),
                                    array(
                                        'name'    => 'FileExtension',
                                        'options' => array(
                                            'extension' => 'jpg, png',
                                        ),
                                    ),
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
                            ),
                        ),
                    )
                );
            }
        } else {
            foreach ($this->event->getOptions() as $option) {
                $this->add(
                    array(
                        'type'       => 'select',
                        'name'       => 'option_' . $option->getId() . '_number_member',
                        'label'      => $option->getPriceNonMembers() != 0 ? ucfirst($option->getName()) . ' (Member)' : ucfirst($option->getName()),
                        'attributes' => array(
                            'options' => $this->getNumberOptions(),
                        ),
                        'options'    => array(
                            'input' => array(
                                'required' => true,
                            ),
                        ),
                    )
                );

                if (!$this->event->isOnlyMembers() && $option->getPriceNonMembers() != 0) {
                    $this->add(
                        array(
                            'type'       => 'select',
                            'name'       => 'option_' . $option->getId() . '_number_non_member',
                            'label'      => ucfirst($option->getName()) . ' (Non Member)',
                            'attributes' => array(
                                'options' => $this->getNumberOptions(),
                            ),
                            'options'    => array(
                                'input' => array(
                                    'required' => true,
                                ),
                            ),
                        )
                    );
                }
            }
        }

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'images',
                'label'      => 'Hierbij geef ik toestemming om beeldmateriaal van mij te maken en te gebruiken (indien een gepubliceerde foto niet gewenst is kan je een mail sturen naar communicatie@vtk.be)',
                'attributes' => array(
                    'id' => 'images',
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

        $this->add(
            array(
                'type'       => 'checkbox',
                'name'       => 'conditions',
            //                'label'      => 'I have read and accept the GDPR terms and condition specified above',
                'label'      => 'Bij deze ga ik akkoord dat VTK mijn gegevens mag gebruiken voor de werking van deze activiteit, om te gebruiken voor noodgevallen en mij te contacteren. Na afloop van de contacttracing worden mijn gegevens verwijderd.',
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
