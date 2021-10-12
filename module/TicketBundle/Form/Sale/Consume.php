<?php

namespace TicketBundle\Form\Sale;

class Consume extends \CommonBundle\Component\Form\Bootstrap\Form
{
    public function init()
    {
        parent::init();

        $this->add(
            array(
                'type' => 'text',
                'name' => 'username',
                'label' => 'Student Number',
                'required' => true,
                'attributes' => array(
                    'autocomplete' => 'off',
                    'id'           => 'username',
                    'placeholder'  => 'Student Number',
                ),
            )
        );

        $this->add(
            array(
                'type' => 'text',
                'name' => 'amount',
                'label' => 'The amount to consume',
                'required' => true,
                'attributes' => array(
                    'id' => 'amount',
                ),
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

//        $this->add(
//            array(
//                'type'       => 'button',
//                'name'       => 'submit',
//                'label'      => 'Consume',
//                'attributes' => array(
//                    'id' => 'consume',
//                ),
//            )
//        );
      $this->addSubmit('Consume', 'sale_consume', 'consume', array('id' => 'sale_consume'));

    }
}
