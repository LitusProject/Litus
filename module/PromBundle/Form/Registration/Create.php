<?php

namespace PromBundle\Form\Registration;

/**
 * 'Login' for new registration
 *
 * @author Mathijs Cuppens
 */
class Create extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'fieldset',
                'name'     => 'create',
                'label'    => '',
                'elements' => array(
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
                                    array('name' => 'CodeUsed'),
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Proceed', 'btn btn-default');
    }
}
