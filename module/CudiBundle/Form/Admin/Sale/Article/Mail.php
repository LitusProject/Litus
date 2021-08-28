<?php

namespace CudiBundle\Form\Admin\Sale\Article;

/**
 * Mail
 *
 * @author Kristof Mariën <kristof.marien@litus.cc>
 */
class Mail extends \CommonBundle\Component\Form\Admin\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type'       => 'select',
                'name'       => 'to',
                'label'      => 'To',
                'required'   => true,
                'attributes' => array(
                    'multiple' => true,
                    'options'  => array(
                        'booked'   => 'Booked',
                        'assigned' => 'Assigned',
                        'sold'     => 'Sold',
                    ),
                ),
            )
        );

        $this->add(
            array(
                'type'       => 'text',
                'name'       => 'subject',
                'label'      => 'Subject',
                'required'   => true,
                'attributes' => array(
                    'style' => 'width: 350px;',
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
                'name'       => 'message',
                'label'      => 'Message',
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

        $this->addSubmit('Send', 'mail');
    }
}
