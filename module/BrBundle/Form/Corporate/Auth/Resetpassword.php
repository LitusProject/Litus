<?php

namespace BrBundle\Form\Corporate\Auth;

/**
 * Reset password form
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class Resetpassword extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
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
                    ),
                ),
            )
        );

        $this->addSubmit('Reset password', 'btn btn-default pull-right');
    }
}
