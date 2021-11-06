<?php

namespace MailBundle\Form\Admin\Message;

/**
 * Edit Message
 *
 * @author Pieter Maene <pieter.maene@litus.cc>
 */
class Edit extends \CommonBundle\Component\Form\Admin\Form
{
    protected $hydrator = 'MailBundle\Hydrator\Message';

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'subject',
                'label'      => 'Subject',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 400px;',
                ),
                'options'    => array(
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
                'type'       => 'textarea',
                'name'       => 'body',
                'label'      => 'Body',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 500px; height: 200px;',
                ),
                'options'    => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Save', 'mail_edit');
    }
}
