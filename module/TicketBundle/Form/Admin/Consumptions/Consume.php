<?php

namespace TicketBundle\Form\Admin\Consumptions;

use TicketBundle\Entity\Consumptions;

class Consume extends \CommonBundle\Component\Form\Admin\Form
{
    protected $consume;

    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type' => 'text',
                'name' => 'consume',
                'label' => 'The amount to consume',
//                'value' => 0,
                'required' => true,
                'options' => array(
                    'input' => array(
                        'filters' => array(
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array('name' => 'int'),
                            array(
                                'name' => 'greaterthan',
                                'options' => array(
                                    'min' => 0,
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $this->addSubmit('Consume', 'consumptions_add');

        if($this->consume !== null) {
            $this->bind($this->consume);
        }
    }
}