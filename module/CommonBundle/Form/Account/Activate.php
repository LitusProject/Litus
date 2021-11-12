<?php

namespace CommonBundle\Form\Account;

/**
 * Account activate form.
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Activate extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'password',
                'name'     => 'credential',
                'label'    => 'Password',
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

        $this->add(
            array(
                'type'     => 'password',
                'name'     => 'verify_credential',
                'label'    => 'Verify Password',
                'required' => true,
                'options'  => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name'    => 'Identical',
                                'options' => array(
                                    'token' => 'credential',
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Activate');
    }
}
