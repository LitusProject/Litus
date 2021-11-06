<?php

namespace CudiBundle\Form\Sale\Queue;

/**
 * Sign in to queue
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class SignIn extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'username',
                'label'      => 'Student Number',
                'required'   => true,
                'attributes' => array(
                    'autocomplete' => 'off',
                    'id'           => 'username',
                    'placeholder'  => 'Student Number',
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'button',
                'name'       => 'submit',
                'label'      => 'Sign In',
                'attributes' => array(
                    'id' => 'signin',
                ),
            )
        );

        $this->add(
            array(
                'type'  => 'reset',
                'name'  => 'cancel',
                'value' => 'Cancel',
            )
        );
    }
}
