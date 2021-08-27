<?php

namespace PromBundle\Form\Registration;

/**
 * 'Login' for new registration
 *
 * @author Mathijs Cuppens
 */
class Manage extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'manage',
                'label'    => '',
                'elements' => array(
                    array(
                        'type'     => 'text',
                        'name'     => 'email',
                        'label'    => 'Email',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'EmailAddress'),
                                    array('name' => 'CodeEmail'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'type'     => 'text',
                        'name'     => 'ticket_code',
                        'label'    => 'Ticket Code',
                        'required' => true,
                        'options'  => array(
                            'input' => array(
                                'filters' => array(
                                    array('name' => 'StringTrim'),
                                ),
                                'validators' => array(
                                    array('name' => 'CodeExists'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Manage', 'btn btn-default');
    }
}
