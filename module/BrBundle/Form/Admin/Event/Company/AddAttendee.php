<?php

namespace BrBundle\Form\Admin\Event\Company;

class AddAttendee extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'BrBundle\Hydrator\Event\CompanyAttendee';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'first_name',
                'label'      => 'First Name',
                'required'   => true,
                'value'      => '',
                'attributes' => array(
                    'id' => 'first_name',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'last_name',
                'label'      => 'Last Name',
                'required'   => true,
                'value'      => '',
                'attributes' => array(
                    'id' => 'last_name',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );
        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'phone_number',
                'label'      => 'Phone Number',
                'attributes' => array(
                    'placeholder' => '+CCAAANNNNNN',
                ),
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'PhoneNumber'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'     => 'text',
                'name'     => 'email',
                'label'    => 'E-mail',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'EmailAddress'),
                        ),
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'lunch',
                'label' => 'Lunch',
                'value' => false,
            )
        );

        $this->add(
            array(
                'type'  => 'checkbox',
                'name'  => 'veggie',
                'label' => 'Veggie',
                'value' => false,
            )
        );

        $this->add(
            array(
                'type'       => 'submit',
                'name'       => 'add_attendee',
                'value'      => 'Add attendee',
                'attributes' => array(
                    'class' => 'add',
                ),
            )
        );
    }
}
