<?php

namespace LogisticsBundle\Form\Admin\Request;

/**
 * Reject a request.
 *
 * @author Mathijs Cuppens <mathijs.cuppens@litus.cc>
 */
class Reject extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'     => 'textarea',
                'name'     => 'reject_reason',
                'label'    => 'Reject Reason',
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

        $this->addSubmit('Reject', 'reject_request');
    }
}
